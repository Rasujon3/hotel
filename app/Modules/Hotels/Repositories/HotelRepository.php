<?php

namespace App\Modules\Hotels\Repositories;


use App\Models\User;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Expenses\Models\Expense;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Hotels\Models\HotelImg;
use App\Modules\Hotels\Models\PropertyType;
use App\Modules\Packages\Models\Package;
use App\Modules\PopularPlaces\Models\PopularPlace;
use App\Modules\Receptionists\Models\Receptionist;
use App\Modules\Rooms\Models\Room;
use App\Services\S3Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class HotelRepository
{
    public function packageList()
    {
        $data = Package::get();

        return $data;
    }
    public function popularPlaceList()
    {
        $data = PopularPlace::get();

        return $data;
    }
    public function propertyTypeList()
    {
        $data = PropertyType::latest()->get();

        return $data;
    }
    public function all($userId, $userTypeId)
    {
        $data = [];
        if ($userTypeId == 4) {
            $hotelId = Receptionist::where('user_id', $userId)->value('hotel_id');
            $data = Hotel::with('images', 'package')->where('id', $hotelId)->get();
        } else {
            $data = Hotel::with('images', 'package')->where('user_id',$userId)->get();
        }

        return $data;
    }
    public function revenueTracker($hotelId, $startDate, $endDate)
    {
        try {
            $income = 0;
            $expense = 0;
            if ($startDate && $endDate) {
                // ✅ Normalize to full-day range
                $startDateTime = Carbon::parse($startDate)->startOfDay();
                $endDateTime   = Carbon::parse($endDate)->endOfDay();

                // ✅ Sum bookings where the booking period overlaps the selected range
                $income = Booking::where('hotel_id', $hotelId)
                    ->where(function ($query) use ($startDate, $endDate) {
//                    ->where(function ($query) use ($startDateTime, $endDateTime) {
                        $query->whereBetween('booking_start_date', [$startDate, $endDate])
                            ->orWhereBetween('booking_end_date', [$startDate, $endDate]);
//                        $query->whereBetween('booking_start_date', [$startDateTime, $endDateTime])
//                            ->orWhereBetween('booking_end_date', [$startDateTime, $endDateTime]);
                    })
                    ->sum('paid');
                // ✅ Filter expenses within the same date range (if you also want date-filtered expenses)
                $expense = Expense::where('hotel_id', $hotelId)
                    ->whereBetween('updated_at', [$startDate, $endDate])
                    ->sum('amount');
            } else {
                $income = Booking::where('hotel_id', $hotelId)->sum('paid');
                $expense = Expense::where('hotel_id', $hotelId)->sum('amount');
            }

            $earn = $income - $expense;

            return [
                'income' => $income,
                'expense' => $expense,
                'earn' => $earn
            ];
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in revenueTracker data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'income' => 0,
                'expense' => 0,
                'earn' => 0
            ];
        }
    }
    public function checkBalance($hotelId)
    {
        $data = Hotel::where('id', $hotelId)->value('balance');

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $data['calculate_booking_price'] = $this->calculateBookingPrice($data['price'], $data['booking_price']);
            // Create the Area record in the database
            $created = Room::create($data);

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
    public function update(Hotel $hotel, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $userId)->first();
            $user->full_name = $data['full_name'] ?? $user->full_name;
            $user->email = $data['email'] ?? $user->email;
            $user->update();

            $previousPercentage = $hotel->booking_percentage;
            $checkIn  = $data['check_in_time'] ? Carbon::parse($data['check_in_time'])->format('H:i:s') : $hotel->check_in_time;
            $checkOut = $data['check_out_time'] ? Carbon::parse($data['check_out_time'])->format('H:i:s') : $hotel->check_out_time;

            // Perform the update
            $hotel->hotel_name = $data['hotel_name'] ?? $hotel->hotel_name;
            $hotel->hotel_address = $data['hotel_address'] ?? $hotel->hotel_address;
            $hotel->hotel_description = $data['hotel_description'] ?? $hotel->hotel_description;
            $hotel->booking_percentage = $data['booking_percentage'] ?? $hotel->booking_percentage;
            $hotel->popular_place_id = $data['popular_place_id'] ?? $hotel->popular_place_id;
            $hotel->property_type_id = $data['property_type_id'] ?? $hotel->property_type_id;
            $hotel->system_commission = $data['system_commission'] ?? $hotel->system_commission;
            $hotel->check_in_time = $checkIn;
            $hotel->check_out_time = $checkOut;
            $hotel->status = $data['status'] ?? $hotel->status;
            $hotel->update();

            if (
                isset($data['booking_percentage']) &&
                $data['booking_percentage'] != $previousPercentage
            ) {
                $rooms = Room::where('hotel_id', $hotel->id)->get();

                foreach ($rooms as $room) {
                    $room->booking_price = $this->calculateBookingPrice(
                        $room->rent,
                        $hotel->booking_percentage
                    );
                    $room->save();
                }
            }

            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);

                $oldImages = HotelImg::where('hotel_id', $hotel->id)->get();
                if (count($oldImages) > 0) {
                    foreach ($oldImages as $img) {
                        if ($img->image_path) {
                            $s3->delete($img->image_path);
                        }
                        $img->delete();
                    }
                }

                $hotelId = $hotel->id;
                if (!empty($data['images'])) {
                    foreach ($data['images'] as $file) {
                        $image_url = null;
                        $image_path = null;

                        $result = $s3->upload($file, 'hotel');

                        if ($result) {
                            $image_url = $result['url'];
                            $image_path = $result['path'];
                        }

                        HotelImg::create([
                            'user_id'   => $userId,
                            'hotel_id'  => $hotelId,
                            'image_url'   => $image_url,
                            'image_path'  => $image_path,
                        ]);
                    }
                }
            }

            DB::commit();
            $hotel = Hotel::with('images')->where('id', $hotel->id)->first();
            return $hotel;
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
    public function delete(Room $room)
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $room->delete();

            if (!$deleted) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting data: ' , [
                'id' => $room->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id, $userId)
    {
        return Hotel::with('package', 'images')
            ->where('user_id', $userId)
            ->find($id);
    }
    public function checkExist($userId, $hotelId, $floorId)
    {
        $checkValid = Floor::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('id', $floorId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function checkEmailExist($userId, $email)
    {
        $checkEmailExist = User::where('email', $email)
            ->where('id', '!=', $userId)
            ->exists();
        return $checkEmailExist;
    }
    public function checkNameUpdateExist($id, $userId, $hotelId,$floorId,$roomNo)
    {
        $checkNameExist = Room::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->where('id', '!=', $id)
            ->exists();

        return $checkNameExist;
    }
    private function calculateBookingPrice($price, $bookingPrice) {
        $value = ($price * $bookingPrice) / 100;
        return ceil($value);
    }
    public function checkBookingPercentage($userId, $hotelId)
    {
        $checkBookingPercentage = Hotel::where('user_id', $userId)
            ->where('id', $hotelId)
            ->value('booking_percentage');

        return $checkBookingPercentage;
    }
}
