<?php

namespace App\Modules\Homes\Repositories;

use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Hotels\Models\HotelImg;
use App\Modules\Hotels\Models\PropertyType;
use App\Modules\Offers\Models\Offer;
use App\Modules\PopularPlaces\Models\PopularPlace;
use App\Modules\Ratings\Models\Rating;
use App\Modules\Receptionists\Models\Receptionist;
use App\Modules\Rooms\Models\Room;
use App\Services\S3Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeRepository
{
    public function popularHotelImages()
    {
        return Cache::remember('popular_hotel_images', now()->addMinutes(10), fn() => $this->getPopularHotelImagesData());
    }
    public function popularHotels()
    {
//        return $this->getPopularHotelsData();
        return Cache::remember('popular_hotels', now()->addMinutes(10), fn() => $this->getPopularHotelsData());
    }
    public function searchByArea($range, $userLat, $userLong)
    {
        $rangeInKm = $range / 1000;

        $data = Hotel::with('images')
            ->select('*')
            ->selectRaw("
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(lat)) *
                cos(radians(`long`) - radians(?)) +
                sin(radians(?)) *
                sin(radians(lat))
            )) AS distance
        ", [$userLat, $userLong, $userLat])
            ->where('status', 'Active')
            ->having('distance', '<=', $rangeInKm)
            ->orderBy('distance', 'asc')
            ->get();

        return $data;
    }
    public function propertyType()
    {
//        return $this->getPropertyTypeData();
        return Cache::remember('property_type', now()->addMinutes(10), fn() => $this->getPropertyTypeData());
    }
    public function getPopularHotelImagesData()
    {
        $data =  HotelImg::with('hotel')
            ->latest()
            ->get();
        return $data;
    }
    public function getPopularHotelsData()
    {
        $data = Hotel::with('ratings', 'images')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('status', 'Active')
            ->paginate(10);

        // map on the paginator items
        $data->getCollection()->transform(function ($hotel) {
            return [
                'id'           => $hotel->id,
                'name'         => $hotel->hotel_name,
                'address'      => $hotel->hotel_address,
                'avg_rating'   => round($hotel->ratings_avg_rating, 1),
                'rating_count' => $hotel->ratings_count,
                'images'       => $hotel->images,
            ];
        });

        return $data;
    }
    public function getPropertyTypeData()
    {
        return PropertyType::where('status', 'Active')
            ->withCount(['hotels' => fn($q) => $q->where('status', 'Active')])
            ->orderBy('hotels_count', 'desc')
            ->get()
            ->map(fn($type) => [
                'id' => $type->id,
                'name' => $type->name,
                'image_url' => $type->image_url,
                'count' => $type->hotels_count,
            ]);
    }
    public function hotelDetails($hotelId)
    {
        return $this->getHotelDetailsData($hotelId);
//        return Cache::remember('hotel_details', now()->addMinutes(10), fn() => $this->getHotelDetailsData($hotelId));
    }
    public function getHotelDetailsData($hotelId)
    {
        $hotel = Hotel::with([
            'facilities',
            'images',
            'ratings',
            'buildings' => function ($q) {
                $q->with([
                    'images',
                    'floors' => function ($q2) {
                        $q2->with('images');
                    }
                ])->where('status', 'Active');
            },
        ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('id', $hotelId)
            ->where('status', 'Active')
            ->first();

        $avgRating = round($hotel->ratings_avg_rating ?? 0, 1);

        return [
            'id'            => $hotel->id,
            'name'          => $hotel->hotel_name,
            'address'       => $hotel->hotel_address,
            'description'   => $hotel->hotel_description,
            'lat'           => $hotel->lat,
            'long'          => $hotel->long,
            'avg_rating'    => $avgRating,
            'rating_count'  => $hotel->ratings_count,
            'rating_status' => $this->getRatingStatus($avgRating),
            'check_in_time' => $hotel->check_in_time,
            'check_out_time' => $hotel->check_out_time,
            'facilities'    => $hotel->facilities,
            'images'        => $hotel->images,
            'buildings'     => $hotel->buildings->map(function ($building) {
                return [
                    'id'     => $building->id,
                    'name'   => $building->name,
                    'status' => $building->status,
                    'images' => $building->images,
                    'floors' => $building->floors->map(function ($floor) {
                        return [
                            'id'     => $floor->id,
                            'name'   => $floor->name,
                            'status' => $floor->status,
                            'images' => $floor->images,
                        ];
                    }),
                ];
            }),
        ];
    }
    private function getRatingStatus(float $avgRating): string
    {
        if ($avgRating >= 5) {
            return 'Very Good';
        } elseif ($avgRating >= 4.5) {
            return 'Good';
        } elseif ($avgRating >= 4) {
            return 'Average';
        } elseif ($avgRating >= 3) {
            return 'Below Average';
        } elseif ($avgRating > 0) {
            return 'Poor';
        } else {
            return 'No Rating';
        }
    }
    public function roomDetails($hotelId, $buildingId, $floorId, $bookingStartDate = null, $bookingEndDate = null)
    {
        // Count available rooms considering date range
        $availableRooms = Room::where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
            ->where('status', 'Active')
            ->where(function ($query) use ($bookingStartDate, $bookingEndDate) {
                $query->whereNull('start_booking_time')
                    ->orWhere(function ($q) use ($bookingStartDate, $bookingEndDate) {
                        $q->where(function ($sub) use ($bookingStartDate, $bookingEndDate) {
                            $sub->where('end_booking_time', '<=', $bookingStartDate); // booking ends before new booking starts
                            #->orWhere('start_booking_time', '>', $bookingEndDate); // booking starts after new booking ends
                        });
                    });
            })
            ->count();

        // Fetch available room data
        $data = Room::with('floor', 'images', 'hotel')
            ->where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->where('status', 'Active')
            ->where(function ($query) use ($bookingStartDate, $bookingEndDate) {
                $query->whereNull('start_booking_time')
                    ->orWhere(function ($q) use ($bookingStartDate, $bookingEndDate) {
                        $q->where(function ($sub) use ($bookingStartDate, $bookingEndDate) {
                            $sub->where('end_booking_time', '<=', $bookingStartDate); // booking ends before new booking starts
                                #->orWhere('start_booking_time', '>', $bookingEndDate);
                        });
                    });
            })
            ->get();

        return [
            'availableRooms' => $availableRooms,
            'data' => $data
        ];
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            // Create the record in the database
            $created = Rating::create($data);

            DB::commit();

            return $created;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function update(Rating $rating, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            // Perform the update
            $rating->update($data);

            DB::commit();
            return $this->find($rating->id, $userId);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    // In FloorRepository.php
    public function delete(Rating $rating)
    {
        DB::beginTransaction();
        try {
            // 5. Finally, delete the data itself
            $rating->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting data: ' , [
                'id' => $rating->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Rating::find($id);
    }
    public function checkValid($userId, $hotelId)
    {
        $checkValid = Rating::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->exists();

        return $checkValid;
    }
    public function checkNameExist($hotelId, $name)
    {
        $checkNameExist = Facility::where('hotel_id', $hotelId)
            ->where('name', $name)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $userId, $hotelId, $name)
    {
        $checkNameExist = Facility::where('hotel_id', $hotelId)
            ->where('name', $name)
            ->where('id', '!=', $id)
            ->exists();
        return $checkNameExist;
    }

    public function myHotelList($userId){
        $data = Hotel::where('user_id',$userId)->get();
        return $data;
    }
    public function hotelsByPopularPlace($popularPlaceId)
    {
        $data =  Hotel::with('ratings')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('popular_place_id', $popularPlaceId)
            ->where('status', 'Active')
            ->paginate(10);
        // map on the paginator items
        $data->getCollection()->transform(function ($hotel) {
            return [
                'id'           => $hotel->id,
                'name'         => $hotel->hotel_name,
                'address'      => $hotel->hotel_address,
                'avg_rating'   => round($hotel->ratings_avg_rating, 1),
                'rating_count' => $hotel->ratings_count,
                'images'       => $hotel->images,
            ];
        });
        return $data;
    }
    public function hotelByPropertyType($propertyTypeId)
    {
        $data =  Hotel::with('ratings')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('property_type_id', $propertyTypeId)
            ->where('status', 'Active')
            ->paginate(10);
        // map on the paginator items
        $data->getCollection()->transform(function ($hotel) {
            return [
                'id'           => $hotel->id,
                'name'         => $hotel->hotel_name,
                'address'      => $hotel->hotel_address,
                'avg_rating'   => round($hotel->ratings_avg_rating, 1),
                'rating_count' => $hotel->ratings_count,
                'images'       => $hotel->images,
            ];
        });
        return $data;
    }
    public function popularPlaces()
    {
        return Cache::remember('popular_places', now()->addMinutes(10), fn() => $this->getPopularPlacesData());
    }
    public function getPopularPlacesData()
    {
        $data =  PopularPlace::where('status', 'Active')->paginate(10);
        return $data;
    }
    public function weeklyOffer()
    {
        // 1. Calculate today and 6 days ahead
        $today = Carbon::today();
        $endDate = Carbon::today()->addDays(6);

        // 2. Fetch offers where the offer period overlaps with today → next 6 days
        $offers = Offer::with([
            'hotel' => function ($q) {
                $q->withAvg('ratings', 'rating')  // ✅ average rating per hotel
                ->withCount('ratings')          // ✅ rating count per hotel
                ->with('images');
            }
        ])
            ->where(function ($query) use ($today, $endDate) {
                $query->whereBetween('start_date', [$today, $endDate])
                    ->orWhereBetween('end_date', [$today, $endDate])
                    ->orWhere(function ($q) use ($today, $endDate) {
                        $q->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->orderBy('start_date', 'asc')
            ->paginate(10);

        // 3. Prepare heading text (e.g., "Save on stays for 20 September - 27 September")
        $heading = "Save on stays for " . $today->format('d F') . " - " . $endDate->format('d F');

        // 4. Return API response
        return [
            'heading' => $heading,
            'offers'  => $offers,
        ];
    }
}
