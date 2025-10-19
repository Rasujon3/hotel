<?php

namespace App\Modules\Ratings\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Facilities\Repositories\FacilityRepository;
use App\Modules\Facilities\Requests\FacilityRequest;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use App\Modules\Ratings\Repositories\RatingRepository;
use App\Modules\Ratings\Requests\RatingRequest;
use Illuminate\Http\Request;

class RatingController extends AppBaseController
{
    protected RatingRepository $ratingRepository;

    public function __construct(RatingRepository $ratingRepo)
    {
        $this->ratingRepository = $ratingRepo;
    }

    // Fetch all data
    public function index(RatingRequest $request)
    {
        $userId = $request->user_id ?? null;
        $hotelId = $request->hotel_id ?? null;

        if (!$userId && !$hotelId) {
            return $this->sendError('Nothing found.', 400);
        }

        $data = $this->ratingRepository->all($userId, $hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(RatingRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $userTypeId = getUser()?->user_type_id;

        $checkValid = $this->ratingRepository->checkValid($userId, $hotelId, $userTypeId);
        if ($checkValid) {
            return $this->sendError('Already added rating for this hotel.', 409);
        }

        $checkBookingStatus = $this->ratingRepository->checkBookingStatus($userId, $hotelId);
        if (!$checkBookingStatus) {
            return $this->sendError('You are not eligible for review this hotel.', 409);
        }

        $store = $this->ratingRepository->store($request->all(),$userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($floor)
    {
        $userId = getUser()?->id;

        $data = $this->ratingRepository->find($floor, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update data
    public function update(RatingRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;

        $data = $this->ratingRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

//        $checkValid = $this->ratingRepository->checkValid($userId, $hotelId);
//        if ($checkValid) {
//            return $this->sendError('Already added rating for this hotel.', 409);
//        }

        $updated = $this->ratingRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [FC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $userId = getUser()?->id;

        $data = $this->ratingRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->ratingRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
