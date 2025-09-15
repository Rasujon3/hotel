<?php

namespace App\Modules\WithdrawalMethods\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Packages\Requests\PackageRequest;
use App\Modules\Rooms\Repositories\RoomRepository;
use App\Modules\Rooms\Requests\RoomRequest;
use App\Modules\WithdrawalMethods\Repositories\WithdrawalMethodRepository;
use App\Modules\WithdrawalMethods\Requests\WithdrawalMethodRequest;
use GuzzleHttp\Psr7\Request;

class WithdrawalMethodController extends AppBaseController
{
    protected WithdrawalMethodRepository $withdrawalMethodRepository;

    public function __construct(WithdrawalMethodRepository $withdrawalMethodRepo)
    {
        $this->withdrawalMethodRepository = $withdrawalMethodRepo;
    }
    // Fetch all data
    public function index(withdrawalMethodRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;

        $checkExist = $this->withdrawalMethodRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $data = $this->withdrawalMethodRepository->all($userId, $hotelId, $floorId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(withdrawalMethodRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $checkExist = $this->withdrawalMethodRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $checkNameExist = $this->withdrawalMethodRepository->checkNameExist($userId, $hotelId, $floorId, $roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 409);
        }

        $checkBookingPercentage = $this->withdrawalMethodRepository->checkBookingPercentage($userId, $hotelId);
        if (!$checkBookingPercentage) {
            return $this->sendError('Please add booking percentage.', 400);
        }

        $store = $this->withdrawalMethodRepository->store($request->all(), $userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }
        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($id)
    {
        $data = $this->withdrawalMethodRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(withdrawalMethodRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $data = $this->withdrawalMethodRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->withdrawalMethodRepository->checkNameUpdateExist($data->id, $userId, $hotelId,$floorId,$roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 404);
        }

        $updated = $this->withdrawalMethodRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [PC-02]', 500);
        }

        return $this->sendResponse($id, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->withdrawalMethodRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->withdrawalMethodRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
