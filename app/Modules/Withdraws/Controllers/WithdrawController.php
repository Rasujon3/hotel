<?php

namespace App\Modules\Withdraws\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Withdraws\Repositories\WithdrawRepository;
use App\Modules\Withdraws\Requests\WithdrawRequest;

class WithdrawController extends AppBaseController
{
    protected WithdrawRepository $withdrawRepository;

    public function __construct(WithdrawRepository $withdrawRepo)
    {
        $this->withdrawRepository = $withdrawRepo;
    }
    // Fetch all data
    public function index()
    {
        $data = $this->withdrawRepository->all();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(WithdrawRequest $request)
    {
        $user = getUser();

        $hotelId = $request->hotel_id;
        $amount = $request->amount;

        $checkWithdrawalMethodExist = $this->withdrawRepository->checkWithdrawalMethodExist($hotelId);
        if (!$checkWithdrawalMethodExist) {
            return $this->sendError('No withdrawal method found.', 404);
        }

        $checkBalance = $this->withdrawRepository->checkBalance($hotelId, $amount);
        if (!$checkBalance) {
            return $this->sendError('You can not withdraw more than balance.', 409);
        }

        $store = $this->withdrawRepository->store($request->all(), $user?->id, $hotelId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [WMC-01]', 500);
        }
        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($id)
    {
        $data = $this->withdrawRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(WithdrawRequest $request, $id)
    {
        $user = getUser();

        $data = $this->withdrawRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found.');
        }

        $hotelId = $request->hotel_id;
        $amount = $request->amount;

        $checkWithdrawalMethodExist = $this->withdrawRepository->checkWithdrawalMethodExist($hotelId);
        if (!$checkWithdrawalMethodExist) {
            return $this->sendError('No withdrawal method found.', 404);
        }

        if ($amount) {
            $checkBalance = $this->withdrawRepository->checkBalance($hotelId, $amount);
            if (!$checkBalance) {
                return $this->sendError('You can not withdraw more than balance.', 409);
            }
        }

        $updated = $this->withdrawRepository->update($data, $request->all(), $user?->id, $hotelId, $amount);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [WC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->withdrawRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->withdrawRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
