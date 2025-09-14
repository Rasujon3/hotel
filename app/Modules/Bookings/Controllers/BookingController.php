<?php

namespace App\Modules\Bookings\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Bookings\Repositories\BookingRepository;
use App\Modules\Bookings\Requests\BookingRequest;
use App\Modules\Packages\Requests\PackageRequest;
use App\Modules\Rooms\Repositories\RoomRepository;
use App\Modules\Rooms\Requests\RoomRequest;
use GuzzleHttp\Psr7\Request;

class BookingController extends AppBaseController
{
    protected BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepository = $bookingRepo;
    }
    // Fetch all data
    public function index(BookingRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;

        $checkExist = $this->bookingRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $data = $this->bookingRepository->all($userId, $hotelId, $floorId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(BookingRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $checkExist = $this->bookingRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $checkNameExist = $this->bookingRepository->checkNameExist($userId, $hotelId, $floorId, $roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 409);
        }

        $checkBookingPercentage = $this->bookingRepository->checkBookingPercentage($userId, $hotelId);
        if (!$checkBookingPercentage) {
            return $this->sendError('Please add booking percentage.', 400);
        }

        $store = $this->bookingRepository->store($request->all(), $userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }
        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($id)
    {
        $data = $this->bookingRepository->find($id);
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

        $data = $this->bookingRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->bookingRepository->checkNameUpdateExist($data->id, $userId, $hotelId,$floorId,$roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 404);
        }

        $updated = $this->bookingRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [PC-02]', 500);
        }

        return $this->sendResponse($id, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->bookingRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->bookingRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
