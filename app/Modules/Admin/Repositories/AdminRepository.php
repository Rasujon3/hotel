<?php

namespace App\Modules\Admin\Repositories;

use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Modules\Areas\Models\Area;
use App\Modules\Areas\Models\AreaHistory;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Packages\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminRepository
{
    public function all()
    {
        $data = User::with('hotels')
            ->where('user_type_id', 3)
            ->where('role', 'owner')
            ->get();

        return $data;
    }
    public function hotelList()
    {
        $data = Hotel::with('withdrawMethod')->get();

        return $data;
    }
    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            // Create the Area record in the database
            $created = Package::create($data);

            DB::commit();

            return $created;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Package $package, array $data)
    {
        DB::beginTransaction();
        try {
            // Perform the update
            $package->update($data);

            DB::commit();
            return $package;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Package $package)
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $package->delete();

            if (!$deleted) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting data: ' , [
                'id' => $package->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Package::find($id);
    }
    public function checkExist($id, $hotelId)
    {
        $user = User::where('id', $id)
            ->where('user_type_id', 3)
            ->where('role', 'owner')
            ->where('status', 'Inactive')
            ->exists();

        $hotel = Hotel::where('id', $hotelId)->exists();
        return $user && $hotel;
    }

    public function updateStatus($user_id, $package_id, $hotelId)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $user_id)->first();
            $hotel = Hotel::where('id', $hotelId)->first();

            $user->status = 'Active';
            $user->hotel_id = $hotel->id;
            $user->update();

            $package = Package::where('id', $package_id)->first();

            if ($hotel && $package) {
                $startDate = now();
                $endDate = null;

                switch ($package->duration) {
                    case 'weekly':
                        $endDate = $startDate->copy()->addWeek();
                        break;
                    case 'monthly':
                        $endDate = $startDate->copy()->addMonth();
                        break;
                    case 'yearly':
                        $endDate = $startDate->copy()->addYear();
                        break;
                    default:
                        return false;
                }
            }

            $hotel->status = 'Active';
            $hotel->package_id = $package?->id;
            $hotel->package_start_date = $startDate;
            $hotel->package_end_date = $endDate;
            $hotel->update();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}
