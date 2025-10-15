<?php

namespace App\Modules\Offers\Repositories;

use App\Modules\Expenses\Models\Expense;
use App\Modules\Expenses\Models\ExpenseImg;
use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Offers\Models\Offer;
use App\Modules\Rooms\Models\Room;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OfferRepository
{
    public function all($hotelId)
    {
        $data = Offer::with('hotel')
            ->where('hotel_id', $hotelId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $hotelId = $data['hotel_id'];
            $bookingPercentage = $this->checkBookingPercentage($hotelId);
            $data['booking_price'] = calculateBookingPrice($data['rent'], $bookingPercentage);

            // Create the record in the database
            $created = Offer::create($data);


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
    public function checkBookingPercentage($hotelId)
    {
        $checkBookingPercentage = Hotel::where('id', $hotelId)->value('booking_percentage');

        return $checkBookingPercentage;
    }
    public function update(Offer $offer, array $data)
    {
        DB::beginTransaction();
        try {
            $hotelId = $data['hotel_id'];
            $bookingPercentage = $this->checkBookingPercentage($hotelId);
            $data['booking_price'] = calculateBookingPrice($data['rent'], $bookingPercentage);

            // Perform the update
            $offer->update($data);

            DB::commit();
            // Return the updated record
            return $this->find($offer);
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
    public function delete(Offer $offer)
    {
        DB::beginTransaction();
        try {
            // 5. Finally, delete the floor itself
            $offer->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting data: ' , [
                'id' => $offer->id,
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
        return Offer::with('hotel')->find($id);
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
    public function checkExist($hotelId,$buildingId, $floorId, $roomNo)
    {
        $checkValid = Room::where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->exists();
        return $checkValid;
    }
}
