<?php

namespace App\Modules\Floors\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use Illuminate\Http\Request;

class FloorController extends AppBaseController
{
    protected FloorRepository $floorRepository;

    public function __construct(FloorRepository $floorRepo)
    {
        $this->floorRepository = $floorRepo;
    }

    public function myHotelList()
    {
        $userId = getUser()?->id;

        $data = $this->floorRepository->myHotelList($userId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Fetch all data
    public function index(FloorRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;
        $buildingId = $request->building_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->floorRepository->all($user?->id, $hotelId, $buildingId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(FloorRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $name = $request->name;
        $hotelId = $request->hotel_id;
        $buildingId = $request->building_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $checkNameExist = $this->floorRepository->checkNameExist($hotelId, $name, $buildingId);
        if ($checkNameExist) {
            return $this->sendError('Floor name already exist.', 404);
        }

        $store = $this->floorRepository->store($request->all(), $user?->id);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [FC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($floor)
    {
        $data = $this->floorRepository->find($floor);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update data
    public function update(FloorRequest $request, $floor)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;
        $buildingId = $request->building_id;

        $data = $this->floorRepository->find($floor);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->floorRepository->checkNameUpdateExist($data->id, $hotelId, $name, $buildingId);
        if ($checkNameExist) {
            return $this->sendError('Floor name already exist.', 404);
        }

        $updated = $this->floorRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [FC-02]', 500);
        }

        return $this->sendResponse($floor, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $data = $this->floorRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->floorRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
