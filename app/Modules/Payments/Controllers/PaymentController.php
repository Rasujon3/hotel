<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Packages\Requests\PackageRequest;
use App\Modules\Payments\Repositories\PaymentRepository;
use App\Modules\Payments\Requests\PaymentRequest;
use App\Modules\Rooms\Repositories\RoomRepository;
use App\Modules\Rooms\Requests\RoomRequest;
use GuzzleHttp\Psr7\Request;

class PaymentController extends AppBaseController
{
    protected PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepository = $paymentRepo;
    }
    // Fetch all data
    public function dueList(PaymentRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;
        $phone = $request->phone;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->paymentRepository->dueList($hotelId, $phone);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function dueSearch(PaymentRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;
        $phone = $request->phone;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->paymentRepository->dueSearch($hotelId, $phone);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function collectDue(PaymentRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        # $userTypeId = getUser()?->user_type_id;
        $hotelId = $request->hotel_id;
        # $userId = $request->user_id;
        $bookingId = $request->booking_id;
        $amount = $request->amount;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        #return $this->sendResponse([$userId, $hotelId, $bookingId, $amount], 'Data retrieved successfully.');
        $checkExist = $this->paymentRepository->checkDueZero($bookingId, $hotelId);
        if ($checkExist) {
            return $this->sendError('No due found.', 404);
        }

        $data = $this->paymentRepository->collectDue($bookingId, $hotelId, $amount, $user?->id);
        if (!$data) {
            return $this->sendError('Something went wrong!!! [PC-01]', 500);
        }
        return $this->sendResponse($data, 'Collect due successfully.');
    }
    public function userCollectDue(PaymentRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        # $userTypeId = getUser()?->user_type_id;
        $hotelId = $request->hotel_id;
        # $userId = $request->user_id;
        $bookingId = $request->booking_id;
        $amount = $request->amount;

        /*
        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }
        */

        $checkExist = $this->paymentRepository->checkDueZero($bookingId, $hotelId);
        if ($checkExist) {
            return $this->sendError('No due found.', 404);
        }

        $data = $this->paymentRepository->userCollectDue($request->all(), $user?->id);
        if (!$data) {
            return $this->sendError('Something went wrong!!! [PC-01]', 500);
        }
        return $this->sendResponse($data, 'Collect due successfully.');
    }
    // Store data
    public function store(PaymentRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $checkExist = $this->paymentRepository->checkExist($userId, $hotelId, $floorId);
        if (!$checkExist) {
            return $this->sendError('No data found.', 404);
        }

        $checkNameExist = $this->paymentRepository->checkNameExist($userId, $hotelId, $floorId, $roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 409);
        }

        $checkBookingPercentage = $this->paymentRepository->checkBookingPercentage($userId, $hotelId);
        if (!$checkBookingPercentage) {
            return $this->sendError('Please add booking percentage.', 400);
        }

        $store = $this->paymentRepository->store($request->all(), $userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [RC-01]', 500);
        }
        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($id)
    {
        $data = $this->paymentRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(PaymentRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $floorId = $request->floor_id;
        $roomNo = $request->room_no;

        $data = $this->paymentRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->paymentRepository->checkNameUpdateExist($data->id, $userId, $hotelId,$floorId,$roomNo);
        if ($checkNameExist) {
            return $this->sendError('Room no already exist.', 404);
        }

        $updated = $this->paymentRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [PC-02]', 500);
        }

        return $this->sendResponse($id, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->paymentRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->paymentRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
