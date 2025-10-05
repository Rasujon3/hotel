<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Admin\Repositories\AdminRepository;
use App\Modules\Admin\Requests\AdminRequest;
use App\Modules\Areas\Queries\AreaDatatable;
use App\Modules\Areas\Repositories\AreaRepository;
use App\Modules\Areas\Requests\AreaRequest;
use App\Http\Controllers\AppBaseController;
use App\Modules\Packages\Repositories\PackageRepository;
use App\Modules\Packages\Requests\PackageRequest;

class AdminController extends AppBaseController
{
    protected AdminRepository $adminRepository;

    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepository = $adminRepo;
    }
    // Fetch all data
    public function ownerList()
    {
        $data = $this->adminRepository->all();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    public function hotelList()
    {
        $data = $this->adminRepository->hotelList();
        return $this->sendResponse($data, 'Data retrieved successfully.');
    }

    // Update status data
    public function ownerStatusUpdate(AdminRequest $request)
    {
        $hotelId = $request->hotel_id;
        $checkExist = $this->adminRepository->checkExist($request->user_id, $hotelId);
        if (!$checkExist) {
            return $this->sendError('User or Hotel not Found.', 500);
        }

        $update = $this->adminRepository->updateStatus($request->user_id, $request->package_id, $hotelId);
        if (!$update) {
            return $this->sendError('Something went wrong!!! [AC-01]', 500);
        }

        return $this->sendResponse([], 'Data created successfully!');
    }
    public function ownerWithdrawAdd(AdminRequest $request)
    {
        $hotelId = $request->hotel_id;
        $checkExist = $this->adminRepository->checkExist($request->user_id, $hotelId);
        if (!$checkExist) {
            return $this->sendError('User or Hotel not Found.', 500);
        }

        $update = $this->adminRepository->updateStatus($request->user_id, $request->package_id, $hotelId);
        if (!$update) {
            return $this->sendError('Something went wrong!!! [AC-01]', 500);
        }

        return $this->sendResponse([], 'Data created successfully!');
    }

    // Get single details data
    public function show($package)
    {
        $data = $this->adminRepository->find($package);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        return $this->sendResponse($data, 'Data retrieved successfully.');
    }
    // Update data
    public function update(PackageRequest $request, $package)
    {
        $data = $this->adminRepository->find($package);
        if (!$data) {
            return $this->sendError('Data not found');
        }

        $updated = $this->adminRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [PC-02]', 500);
        }

        return $this->sendResponse($package, 'Data updated successfully!');
    }
    // Delete data
    public function destroy($country)
    {
        $data = $this->adminRepository->find($country);
        if (!$data) {
            return $this->sendError('Data not found');
        }
        $this->adminRepository->delete($data);
        return $this->sendSuccess('Data deleted successfully!');
    }
}
