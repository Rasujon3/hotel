<?php

namespace App\Modules\Expenses\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Expenses\Repositories\ExpenseRepository;
use App\Modules\Expenses\Requests\ExpenseRequest;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use Illuminate\Http\Request;

class ExpenseController extends AppBaseController
{
    protected ExpenseRepository $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepo)
    {
        $this->expenseRepository = $expenseRepo;
    }

    // Fetch all data
    public function index(ExpenseRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->expenseRepository->all($user?->id, $hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(ExpenseRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $store = $this->expenseRepository->store($request->all(),$user?->id);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [EC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($id)
    {
        $userId = getUser()?->id;

        $data = $this->expenseRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update data
    public function update(ExpenseRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;

        $data = $this->expenseRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $updated = $this->expenseRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [EC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $userId = getUser()?->id;

        $data = $this->expenseRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->expenseRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
