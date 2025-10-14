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
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;
        $buildingId = $request->building_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->bookingRepository->all($hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(BookingRequest $request)
    {
        $userId = getUser()?->id;

        $rooms = $request->rooms; // array of selected rooms

        // ✅ 1. Check all rooms are from the same hotel
        $sameHotelCheck = $this->bookingRepository->checkAllRoomsSameHotel($rooms);
        if (!$sameHotelCheck['status']) {
            return $this->sendError($sameHotelCheck['message'], 422);
        }

        // ✅ 2. Loop through each room to validate existence and availability
        $hotelId = null;
        foreach ($rooms as $room) {
            $hotelId = $room['hotel_id'];
            $existCheck = $this->bookingRepository->checkRoomExists(
                $room['hotel_id'],
                $room['floor_id'],
                $room['room_id']
            );
            if (!$existCheck['status']) {
                return $this->sendError($existCheck['message'], 404);
            }

            $bookingStartDate = $room['booking_start_date'];
            $availabilityCheck = $this->bookingRepository->checkRoomAvailability($room['room_id'], $bookingStartDate);
            if (!$availabilityCheck['status']) {
                return $this->sendError($availabilityCheck['message'], 409);
            }
        }

        // ✅ 3. If all checks pass, continue storing booking
        $store = $this->bookingRepository->store($request->all(), $userId, $hotelId);

        if (!$store) {
            return $this->sendError('Something went wrong!!! [BC-01]', 500);
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
    public function updateStatus(BookingRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $status = $request->status;

        return $status === 'checked_in'
            ? $this->updateCheckInStatus($request)
            : $this->updateCheckOutStatus($request);
    }
    public function searchBookingByUser(BookingRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->bookingRepository->searchBookingByUser($request->phone);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function updateCheckInStatus(BookingRequest $request)
    {
        $userId = getUser()?->id;

        $bookingDetailId = $request->booking_detail_id;

        // ✅ 1. Check Booking Status Already Checked-In
        $checkBookingStatusAlreadyCheckedIn = $this->bookingRepository->checkBookingStatusAlreadyCheckedIn($bookingDetailId);
        if ($checkBookingStatusAlreadyCheckedIn) {
            return $this->sendError('Booking already checked in.', 409);
        }

        // ✅ 3. If all checks pass, continue storing booking
        $store = $this->bookingRepository->checkedInStatusUpdate($request->all(), $userId, $bookingDetailId);

        if (!$store) {
            return $this->sendError('Something went wrong!!! [BC-01]', 500);
        }

        return $this->sendResponse($store, 'Checked-In status updated successfully!');
    }
    public function updateCheckOutStatus(BookingRequest $request)
    {
        $userId = getUser()?->id;

        $bookingDetailId = $request->booking_detail_id; // array of selected rooms

        // ✅ 1. Check Booking Status Already Checked-In
        $checkBookingStatusAlreadyCheckedOut = $this->bookingRepository->checkBookingStatusAlreadyCheckedOut($bookingDetailId);
        if ($checkBookingStatusAlreadyCheckedOut) {
            return $this->sendError('Booking already checked out.', 409);
        }

        // ✅ 2. Check have any due amount
        $checkDue = $this->bookingRepository->checkDue($bookingDetailId);
        if ($checkDue) {
            return $this->sendError('Please collect due amount.', 409);
        }

        // ✅ 3. If all checks pass, continue storing booking
        $store = $this->bookingRepository->checkedOutStatusUpdate($request->all(), $userId, $bookingDetailId);

        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }

        return $this->sendResponse($store, 'Checked-Out status updated successfully!');
    }
    public function userBookings(BookingRequest $request)
    {
        $userId = getUser()?->id;
        $bookingId = $request->booking_id ?? null;
        $status = $request->status ?? null;

        $data = $this->bookingRepository->userBookings($userId, $bookingId, $status);

        return $this->sendResponse($data, 'Data retrieved successfully!');
    }
}
