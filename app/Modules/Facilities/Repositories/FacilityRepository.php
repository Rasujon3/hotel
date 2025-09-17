<?php

namespace App\Modules\Facilities\Repositories;

use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Receptionists\Models\Receptionist;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FacilityRepository
{
    public function all($hotelId)
    {
        $data = Facility::with('hotel')
            ->where('hotel_id', $hotelId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $data['created_by'] = $userId;
            // Create the record in the database
            $created = Facility::create($data);

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
    public function update(Facility $facility, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['updated_by'] = $userId;
            $data['updated_at'] = now();
            // Perform the update
            $facility->update($data);

            DB::commit();
            return $this->find($facility->id);
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
    // In FloorRepository.php
    public function delete(Facility $facility)
    {
        DB::beginTransaction();
        try {
            // 5. Finally, delete the data itself
            $facility->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting data: ' , [
                'id' => $facility->id,
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
        return Facility::with('hotel')->find($id);
    }
    public function checkValid($userId, $hotelId, $userTypeId)
    {
        $checkValid = false;
        if ($userTypeId == 4) {
            $checkValid = Receptionist::where('user_id', $userId)
                ->where('hotel_id', $hotelId)
                ->exists();
        } else {
            $checkValid = Hotel::where('user_id', $userId)
                ->where('id', $hotelId)
                ->exists();
        }

        return $checkValid;
    }
    public function checkNameExist($hotelId, $name)
    {
        $checkNameExist = Facility::where('hotel_id', $hotelId)
            ->where('name', $name)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $hotelId, $name)
    {
        $checkNameExist = Facility::where('hotel_id', $hotelId)
            ->where('name', $name)
            ->where('id', '!=', $id)
            ->exists();
        return $checkNameExist;
    }

    public function myHotelList($userId){
        $data = Hotel::where('user_id',$userId)->get();
        return $data;
    }
}
