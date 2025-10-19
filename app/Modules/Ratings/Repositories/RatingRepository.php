<?php

namespace App\Modules\Ratings\Repositories;

use App\Modules\Bookings\Models\Booking;
use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Ratings\Models\Rating;
use App\Modules\Receptionists\Models\Receptionist;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RatingRepository
{
    public function all($userId, $hotelId = null)
    {
        $query = Rating::with('hotel', 'user');

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $data = $query->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            // Create the record in the database
            $created = Rating::create($data);

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
    public function update(Rating $rating, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            // Perform the update
            $rating->update($data);

            DB::commit();
            return $this->find($rating->id, $userId);
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
    public function delete(Rating $rating)
    {
        DB::beginTransaction();
        try {
            // 5. Finally, delete the data itself
            $rating->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting data: ' , [
                'id' => $rating->id,
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
        return Rating::find($id);
    }
    public function checkValid($userId, $hotelId)
    {
        $checkValid = Rating::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->exists();

        return $checkValid;
    }
    public function checkBookingStatus($userId, $hotelId)
    {
        $checkValid = Booking::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            # ->where('status', '!=', 'pending')
            # ->where('status', '!=', 'confirmed')
            ->exists();

        return $checkValid;
    }
    public function checkNameExist($hotelId, $name)
    {
        $checkNameExist = Facility::where('hotel_id', $hotelId)
            ->where('name', $name)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $userId, $hotelId, $name)
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
