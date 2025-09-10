<?php

namespace App\Modules\Rooms\Repositories;

use App\Modules\Floors\Models\Floor;
use App\Modules\Rooms\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RoomRepository
{
    public function all($userId, $hotelId, $floorId)
    {
        $data = Room::with('hotel', 'floor')
            ->where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $data['calculate_booking_price'] = $this->calculateBookingPrice($data['price'], $data['booking_price']);
            // Create the Area record in the database
            $created = Room::create($data);

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
    public function update(Room $room, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $data['calculate_booking_price'] = $this->calculateBookingPrice($data['price'], $data['booking_price']);
            // Perform the update
            $room->update($data);

            DB::commit();
            return $room;
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
    public function delete(Room $room)
    {
        DB::beginTransaction();
        try {
            // Perform soft delete
            $deleted = $room->delete();

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
                'id' => $room->id,
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
        return Room::with('hotel', 'floor')->find($id);
    }
    public function checkExist($userId, $hotelId)
    {
        $checkValid = Floor::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function checkNameExist($userId, $hotelId, $floorId, $roomNo)
    {
        $checkNameExist = Room::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $userId, $hotelId,$floorId,$roomNo)
    {
        $checkNameExist = Room::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->where('id', '!=', $id)
            ->exists();

        return $checkNameExist;
    }
    private function calculateBookingPrice($price, $bookingPrice) {
        $value = ($price * $bookingPrice) / 100;
        return ceil($value);
    }

}
