<?php

namespace App\Modules\WithdrawalMethods\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\WithdrawalMethods\Repositories\WithdrawalMethodRepository;
use App\Modules\WithdrawalMethods\Requests\WithdrawalMethodRequest;

class WithdrawalMethodController extends AppBaseController
{
    protected WithdrawalMethodRepository $withdrawalMethodRepository;

    public function __construct(WithdrawalMethodRepository $withdrawalMethodRepo)
    {
        $this->withdrawalMethodRepository = $withdrawalMethodRepo;
    }
    // Fetch all data
    public function index(WithdrawalMethodRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->withdrawalMethodRepository->all($hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function withdrawalHistory(WithdrawalMethodRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->withdrawalMethodRepository->withdrawalHistory($hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(WithdrawalMethodRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $checkAlreadyAdded = $this->withdrawalMethodRepository->checkAlreadyAdded($hotelId);
        if ($checkAlreadyAdded) {
            return $this->sendError('You can not add more than one withdrawal method.', 409);
        }

        $store = $this->withdrawalMethodRepository->store($request->all(), $user?->id);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [WMC-01]', 500);
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
    public function update(WithdrawalMethodRequest $request, $id)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->withdrawalMethodRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found.');
        }

        $updated = $this->withdrawalMethodRepository->update($data, $request->all(), $user?->id);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [WMC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
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
