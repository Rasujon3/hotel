<?php

namespace App\Modules\Offers\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Expenses\Repositories\ExpenseRepository;
use App\Modules\Expenses\Requests\ExpenseRequest;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use App\Modules\Offers\Repositories\OfferRepository;
use App\Modules\Offers\Requests\OfferRequest;
use Illuminate\Http\Request;

class OfferController extends AppBaseController
{
    protected OfferRepository $offerRepository;

    public function __construct(OfferRepository $offerRepo)
    {
        $this->offerRepository = $offerRepo;
    }

    // Fetch all data
    public function index(OfferRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->offerRepository->all($hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(OfferRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;
        $buildingId = $request->building_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $checkExist = $this->offerRepository->checkExist($hotelId, $buildingId, $floorId, $roomNo);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $store = $this->offerRepository->store($request->all(),$user?->id);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [EC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($id)
    {
        $data = $this->offerRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update data
    public function update(OfferRequest $request, $id)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;
        $buildingId = $request->building_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $checkExist = $this->offerRepository->checkExist($hotelId, $buildingId, $floorId, $roomNo);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $data = $this->offerRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $updated = $this->offerRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [OC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $data = $this->offerRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->offerRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
