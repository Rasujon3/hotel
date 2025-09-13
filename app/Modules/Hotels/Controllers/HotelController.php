<?php

namespace App\Modules\Hotels\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Hotels\Repositories\HotelRepository;
use App\Modules\Hotels\Requests\HotelRequest;

class HotelController extends AppBaseController
{
    protected HotelRepository $hotelRepository;

    public function __construct(HotelRepository $hotelRepo)
    {
        $this->hotelRepository = $hotelRepo;
    }
    // Fetch all data
    public function index()
    {
        $userId = getUser()?->id;

        $data = $this->hotelRepository->all($userId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(HotelRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;

        $checkValid = $this->floorRepository->checkValid($userId, $hotelId);
        if (!$checkValid) {
            return $this->sendError('Hotel not found.', 404);
        }

        $checkNameExist = $this->floorRepository->checkNameExist($userId, $hotelId, $name);
        if ($checkNameExist) {
            return $this->sendError('Floor name already exist.', 404);
        }

        $store = $this->floorRepository->store($request->all(),$userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [FC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($floor)
    {
        $userId = getUser()?->id;

        $data = $this->floorRepository->find($floor, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(HotelRequest $request, $id)
    {
        $userId = getUser()?->id;
        $email = $request->email ?? null;

        $data = $this->hotelRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkEmailExist = $this->hotelRepository->checkEmailExist($userId, $email);
        if ($checkEmailExist) {
            return $this->sendError('Email already exist.', 409);
        }

        $updated = $this->hotelRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [HC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $userId = getUser()?->id;

        $data = $this->floorRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->floorRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
