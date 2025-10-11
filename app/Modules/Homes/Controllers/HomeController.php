<?php

namespace App\Modules\Homes\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Facilities\Repositories\FacilityRepository;
use App\Modules\Facilities\Requests\FacilityRequest;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use App\Modules\Homes\Repositories\HomeRepository;
use App\Modules\Homes\Requests\HomeRequest;
use App\Modules\Ratings\Repositories\RatingRepository;
use App\Modules\Ratings\Requests\RatingRequest;
use Illuminate\Http\Request;

class HomeController extends AppBaseController
{
    protected HomeRepository $homeRepository;

    public function __construct(HomeRepository $homeRepo)
    {
        $this->homeRepository = $homeRepo;
    }

    // Fetch all data
    public function popularHotelImages()
    {
        $data = $this->homeRepository->popularHotelImages();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Fetch all data
    public function popularHotels()
    {
        $data = $this->homeRepository->popularHotels();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function searchByArea(HomeRequest $request)
    {
        $range = $request->range;
        $userLat = $request->lat;
        $userLong = $request->long;

        $data = $this->homeRepository->searchByArea($range, $userLat, $userLong);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function propertyType()
    {
        $data = $this->homeRepository->propertyType();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Fetch all data
    public function hotelDetails(HomeRequest $request)
    {
        $hotelId = $request->hotel_id;

        $data = $this->homeRepository->hotelDetails($hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Fetch all data
    public function roomDetails(HomeRequest $request)
    {
        $hotelId = $request->hotel_id;
        $buildingId = $request->building_id;
        $floorId = $request->floor_id;
        $bookingStartDate = $request->booking_start_date;
        $bookingEndDate = $request->booking_end_date;

        $data = $this->homeRepository->roomDetails($hotelId, $buildingId, $floorId, $bookingStartDate, $bookingEndDate);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function PopularPlaces()
    {
        $data = $this->homeRepository->popularPlaces();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function hotelsByPopularPlace(HomeRequest $request)
    {
        $popularPlaceId = $request->popular_place_id;

        $data = $this->homeRepository->hotelsByPopularPlace($popularPlaceId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function hotelByPropertyType(HomeRequest $request)
    {
        $propertyTypeId = $request->property_type_id;

        $data = $this->homeRepository->hotelByPropertyType($propertyTypeId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function weeklyOffer()
    {
        $data = $this->homeRepository->weeklyOffer();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(HomeRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $userTypeId = getUser()?->user_type_id;
        $name = $request->name;

        $checkValid = $this->homeRepository->checkValid($userId, $hotelId, $userTypeId);
        if ($checkValid) {
            return $this->sendError('Already added rating for this hotel.', 409);
        }

        $store = $this->homeRepository->store($request->all(),$userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($floor)
    {
        $userId = getUser()?->id;

        $data = $this->homeRepository->find($floor, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update data
    public function update(HomeRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;

        $data = $this->homeRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $updated = $this->homeRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [FC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $userId = getUser()?->id;

        $data = $this->homeRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->homeRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
