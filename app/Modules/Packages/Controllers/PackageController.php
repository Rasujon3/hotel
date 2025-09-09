<?php

namespace App\Modules\Packages\Controllers;

use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Http\Controllers\AppBaseController;
use App\Modules\Packages\Repositories\PackageRepository;
use App\Modules\Packages\Requests\PackageRequest;

class PackageController extends AppBaseController
{
    protected PackageRepository $packageRepository;

    public function __construct(PackageRepository $packageRepo)
    {
        $this->packageRepository = $packageRepo;
    }
    // Fetch all data
    public function index()
    {
        $data = $this->packageRepository->all();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Store data
    public function store(PackageRequest $request)
    {
        $store = $this->packageRepository->store($request->all());
        if (!$store) {
            return $this->sendError('Something went wrong!!! [PC-01]', 500);
        }
        return $this->sendResponse($store, 'Data created successfully!');
    }

    // Get single details data
    public function show($package)
    {
        $data = $this->packageRepository->find($package);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(PackageRequest $request, $package)
    {
        $data = $this->packageRepository->find($package);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $updated = $this->packageRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [PC-02]', 500);
        }

        return $this->sendResponse($package, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($country)
    {
        $data = $this->packageRepository->find($country);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->packageRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
