<?php

namespace App\Modules\Floors\Repositories;

use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FloorRepository
{
    public function all($userId, $hotelId)
    {
        $data = Floor::where('hotel_id', $hotelId)
            ->where('user_id', $userId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            // Create the record in the database
            $created = Floor::create($data);

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
    public function update(Floor $floor, array $data)
    {
        DB::beginTransaction();
        try {
            // Perform the update
            $floor->update($data);

            DB::commit();
            return $floor;
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
    public function delete(Floor $floor)
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $floor->rooms()->delete();

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
                'id' => $floor->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id, $userId)
    {
        return Floor::where('user_id', $userId)->find($id);
    }
    public function checkValid($userId, $hotelId)
    {
        $checkValid = Hotel::where('user_id', $userId)
            ->where('id', $hotelId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function checkNameExist($userId, $hotelId, $name)
    {
        $checkNameExist = Floor::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('name', $name)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $userId, $hotelId, $name)
    {
        $checkNameExist = Floor::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
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
