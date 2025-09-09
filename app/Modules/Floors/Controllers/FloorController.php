<?php

namespace App\Modules\Floors\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Floors\Repositories\FloorRepository;
use App\Modules\Floors\Requests\FloorRequest;
use Illuminate\Http\Request;

class FloorController extends AppBaseController
{
    protected FloorRepository $floorRepository;

    public function __construct(FloorRepository $floorRepo)
    {
        $this->floorRepository = $floorRepo;
    }

    // Fetch all data
    public function index(FloorRequest $request)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;

        $checkValid = $this->floorRepository->checkValid($userId, $hotelId);
        if (!$checkValid) {
            return $this->sendError('Hotel not found.', 404);
        }

        $data = $this->floorRepository->all($userId, $hotelId);
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(FloorRequest $request)
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
    public function update(FloorRequest $request, $floor)
    {
        $userId = getUser()?->id;
        $hotelId = $request->hotel_id;
        $name = $request->name;

        $data = $this->floorRepository->find($floor, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $checkNameExist = $this->floorRepository->checkNameUpdateExist($data->id, $userId, $hotelId, $name);
        if ($checkNameExist) {
            return $this->sendError('Floor name already exist.', 404);
        }

        $updated = $this->floorRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [FC-02]', 500);
        }

        return $this->sendResponse($floor, 'Data updated successfully!');
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
