<?php

namespace App\Modules\PopularPlaces\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Hotels\Repositories\HotelRepository;
use App\Modules\Hotels\Requests\HotelRequest;
use App\Modules\PopularPlaces\Models\PopularPlace;
use App\Modules\PopularPlaces\Repositories\PopularPlaceRepository;
use App\Modules\PopularPlaces\Requests\PopularPlaceRequest;

class PopularPlaceController extends AppBaseController
{
    protected PopularPlaceRepository $popularPlaceRepository;

    public function __construct(PopularPlaceRepository $popularPlaceRepo)
    {
        $this->popularPlaceRepository = $popularPlaceRepo;
    }
    // Fetch all data
    public function index()
    {
        $data = $this->popularPlaceRepository->all();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Store data
    public function store(PopularPlaceRequest $request)
    {
        $userId = getUser()?->id;

        $store = $this->popularPlaceRepository->store($request->all(), $userId);
        if (!$store) {
            return $this->sendError('Something went wrong!!! [PPC-01]', 500);
        }

        return $this->sendResponse($store, 'Data created successfully!');
    }
    // Get single details data
    public function show($floor)
    {
        $userId = getUser()?->id;

        $data = $this->popularPlaceRepository->find($floor, $userId);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(PopularPlaceRequest $request, $id)
    {
        $data = $this->popularPlaceRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $updated = $this->popularPlaceRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [HC-02]', 500);
        }

        return $this->sendResponse($updated, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($id)
    {
        $data = $this->popularPlaceRepository->find($id);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->popularPlaceRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
