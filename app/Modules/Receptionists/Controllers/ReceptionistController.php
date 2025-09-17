<?php

namespace App\Modules\Receptionists\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Receptionists\Repositories\ReceptionistRepository;
use App\Modules\Receptionists\Requests\ReceptionistRequest;

class ReceptionistController extends AppBaseController
{
    protected ReceptionistRepository $receptionistRepository;

    public function __construct(ReceptionistRepository $receptionistRepo)
    {
        $this->receptionistRepository = $receptionistRepo;
    }

    // Fetch all data
    public function index(ReceptionistRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->receptionistRepository->all($user?->id, $hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Register
    public function register(ReceptionistRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $register = $this->receptionistRepository->register($request, $user?->id);
        if (!$register) {
            return $this->sendError('Something went wrong!!! [RPC-01]', 500);
        }

        return $this->sendSuccess('Data created successfully.');
    }
    public function updateReceptionist(ReceptionistRequest $request, $id)
    {
        $user = getUser();
        $hotelId = $request->hotel_id;

        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $name = $request->name;
        $email = $request->email;

        $data = $this->receptionistRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $uniqueCheck = $this->receptionistRepository->uniqueCheck($name,$email,$id);
        if ($uniqueCheck) {
            return $this->sendError('Email or phone already exists.');
        }

        $updated = $this->receptionistRepository->receptionistUpdate($data, $request, $user?->id, $id);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [EC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->receptionistRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->receptionistRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
