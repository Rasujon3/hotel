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
        $area = $this->hotelRepository->store($request->all());
        if (!$area) {
            return $this->sendError('Something went wrong!!! [AS-01]', 500);
        }
        return $this->sendResponse($area, 'Area created successfully!');
    }

    // Get single details data
    public function show($area)
    {
        $data = $this->hotelRepository->find($area);
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $summary = $this->hotelRepository->getData($area);
        return $this->sendResponse($summary, 'Area retrieved successfully.');
    }
    // Update data
    public function update(HotelRequest $request, $area)
    {
        $data = $this->hotelRepository->find($area);
        if (!$data) {
            return $this->sendError('Area not found');
        }
        $updated = $this->hotelRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [AU-04]', 500);
        }
        return $this->sendResponse($area, 'Area updated successfully!');
    }
    // bulk update
    public function bulkUpdate(HotelRequest $request)
    {
        $bulkUpdate = $this->hotelRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [ABU-05]', 500);
        }
        return $this->sendResponse([],'Area Bulk updated successfully!');
    }
    // check availability
    public function checkAvailability(HotelRequest $request)
    {
        $checkAvailability = $this->hotelRepository->checkAvailability($request->all());
        if ($checkAvailability) {
            return $this->sendError('Area is already exist!', 500);
        }
        return $this->sendResponse([],'Area is available!');
    }
    // history
    public function history()
    {
        $history = $this->hotelRepository->history();
        return $this->sendResponse($history,'Area history retrieved successfully.');
    }
    public function import(HotelRequest $request)
    {
        $import = $this->hotelRepository->import($request);
        if (!$import) {
            return $this->sendError('Something went wrong!!! [CCBU-06]', 500);
        }
        return $this->sendResponse([],'City imported successfully!');
    }
}
