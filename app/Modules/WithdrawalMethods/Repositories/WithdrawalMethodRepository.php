<?php

namespace App\Modules\WithdrawalMethods\Repositories;

use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use App\Modules\Rooms\Models\RoomImg;
use App\Modules\WithdrawalMethods\Models\WithdrawalMethod;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WithdrawalMethodRepository
{
    public function all($hotelId)
    {
        $data = WithdrawalMethod::with('hotel')
            ->where('hotel_id', $hotelId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;

            // Create the data record in the database
            $created = WithdrawalMethod::create($data);

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
    public function update(WithdrawalMethod $withdrawalMethod, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            // Perform the update
            $withdrawalMethod->update($data);

            DB::commit();
            return $this->find($withdrawalMethod->id);
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
    public function delete(WithdrawalMethod $withdrawalMethod)
    {
        DB::beginTransaction();
        try {
            // 5. Finally, delete itself
            $withdrawalMethod->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting data: ' , [
                'id' => $withdrawalMethod->id,
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
        return WithdrawalMethod::with('hotel')->find($id);
    }
    public function checkExist($userId, $hotelId, $floorId)
    {
        $checkValid = Floor::where('hotel_id', $hotelId)
            ->where('id', $floorId)
            ->exists();
        return $checkValid;
    }
    public function checkAlreadyAdded($hotelId)
    {
        $checkAlreadyAdded = WithdrawalMethod::where('hotel_id', $hotelId)
            ->exists();
        return $checkAlreadyAdded;
    }
    public function checkNameUpdateExist($id, $userId, $hotelId,$floorId,$roomNo)
    {
        $checkNameExist = Room::where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->where('id', '!=', $id)
            ->exists();

        return $checkNameExist;
    }
    public function checkBookingPercentage($userId, $hotelId)
    {
        $checkBookingPercentage = Hotel::where('user_id', $userId)
            ->where('id', $hotelId)
            ->value('booking_percentage');

        return $checkBookingPercentage;
    }
}
