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
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $buildingId = $request->building_id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->roomRepository->all($user?->id, $hotelId, $floorId, $buildingId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(RoomRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $buildingId = $request->building_id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $checkNameExist = $this->roomRepository->checkNameExist($hotelId, $floorId, $roomNo, $buildingId);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 409);
        }

        $checkBookingPercentage = $this->roomRepository->checkBookingPercentage($hotelId);
        if (!$checkBookingPercentage) {
            return $this->sendError('Please add booking percentage.', 400);
        }

        $checkSystemCommission = $this->roomRepository->checkSystemCommission($hotelId);
        if (!$checkSystemCommission) {
            return $this->sendError('Please add system commission.', 400);
        }

        $store = $this->roomRepository->store($request->all(), $user?->id);
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
        $buildingId = $request->building_id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $data = $this->roomRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->roomRepository->checkNameUpdateExist($data->id, $hotelId,$floorId,$roomNo,$buildingId);
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
