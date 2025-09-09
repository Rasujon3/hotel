<?php

namespace App\Modules\Packages\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Areas\Models\AreaHistory;
use App\Modules\Packages\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PackageRepository
{
    public function all()
    {
        $data = Package::get();

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
}
