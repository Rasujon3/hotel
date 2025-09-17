<?php

namespace App\Modules\Facilities\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Facilities\Repositories\FacilityRepository;
use App\Modules\Facilities\Requests\FacilityRequest;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use Illuminate\Http\Request;

class FacilityController extends AppBaseController
{
    protected FacilityRepository $facilityRepository;

    public function __construct(FacilityRepository $facilityRepo)
    {
        $this->facilityRepository = $facilityRepo;
    }

    // Fetch all data
    public function index(FacilityRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $data = $this->facilityRepository->all($hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(FacilityRequest $request)
    {
        $user = getUser();
        $userHotelIds = getUserHotelIds($user?->id, $user?->user_type_id);
        $hotelId = $request->hotel_id;

        if (!in_array($hotelId, $userHotelIds,false)) {
            return $this->sendError('You can not access this data.', 403);
        }

        $name = $request->name;

        $checkNameExist = $this->facilityRepository->checkNameExist($hotelId, $name);
        if ($checkNameExist) {
            return $this->sendError('Facility name already exist.', 409);
        }

        $store = $this->facilityRepository->store($request->all(),$user?->id);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [FLC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($id)
    {
        $data = $this->facilityRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update data
    public function update(FacilityRequest $request, $id)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;

        $data = $this->facilityRepository->find($id, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->facilityRepository->checkNameUpdateExist($data->id, $hotelId, $name);
        if ($checkNameExist) {
            return $this->sendError('Facility name already exist.', 404);
        }

        $updated = $this->facilityRepository->update($data, $request->all(), $userId);
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [FC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }

    // Delete data
    public function destroy($id)
    {
        $userId = getUser()?->id;

        $data = $this->facilityRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->facilityRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
