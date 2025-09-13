<?php

namespace App\Modules\Rooms\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Packages\Requests\PackageRequest;
use App\Modules\Rooms\Repositories\RoomRepository;
use App\Modules\Rooms\Requests\RoomRequest;
use GuzzleHttp\Psr7\Request;

class RoomController extends AppBaseController
{
    protected RoomRepository $roomRepository;

    public function __construct(RoomRepository $roomRepo)
    {
        $this->roomRepository = $roomRepo;
    }
    // Fetch all data
    public function index(RoomRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;

        $checkExist = $this->roomRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $data = $this->roomRepository->all($userId, $hotelId, $floorId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(RoomRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $checkExist = $this->roomRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $checkNameExist = $this->roomRepository->checkNameExist($userId, $hotelId, $floorId, $roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 409);
        }

        $checkBookingPercentage = $this->roomRepository->checkBookingPercentage($userId, $hotelId);
        if (!$checkBookingPercentage) {
            return $this->sendError('Please add booking percentage.', 400);
        }

        $store = $this->roomRepository->store($request->all(), $userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }
        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($id)
    {
        $data = $this->roomRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(RoomRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $data = $this->roomRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->roomRepository->checkNameUpdateExist($data->id, $userId, $hotelId,$floorId,$roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 404);
        }

        $updated = $this->roomRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [PC-02]', 500);
        }

        return $this->sendResponse($id, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->roomRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->roomRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
