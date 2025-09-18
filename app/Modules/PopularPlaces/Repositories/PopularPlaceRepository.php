<?php

namespace App\Modules\PopularPlaces\Repositories;


use App\Models\User;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Expenses\Models\Expense;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Hotels\Models\HotelImg;
use App\Modules\PopularPlaces\Models\PopularPlace;
use App\Modules\Receptionists\Models\Receptionist;
use App\Modules\Rooms\Models\Room;
use App\Services\S3Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PopularPlaceRepository
{
    public function all()
    {
        $data = PopularPlace::get();

        return $data;
    }
    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $image_url = null;
            $image_path = null;

            if (!empty($data['image'])) {
                $s3 = app(S3Service::class);
                $result = $s3->upload($data['image'], 'popular_place');

                if ($result) {
                    $image_url = $result['url'];
                    $image_path = $result['path'];
                }
            }

            $data['image_url'] = $image_url;
            $data['image_path'] = $image_path;

            // Create the Data record in the database
            $created = PopularPlace::create($data);

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
    public function update(PopularPlace $popularPlace, array $data)
    {
        DB::beginTransaction();
        try {
            $image_url = $popularPlace->image_url;
            $image_path = $popularPlace->image_path;

            if (!empty($data['image'])) {
                $s3 = app(S3Service::class);

                $oldImage = $popularPlace->image_path;
                $s3->delete($oldImage);

                $result = $s3->upload($data['image'], 'popular_place');

                if ($result) {
                    $image_url = $result['url'];
                    $image_path = $result['path'];
                }
            }

            // Perform the update
            $popularPlace->name = $data['name'] ?? $popularPlace->name;
            $popularPlace->status = $data['status'] ?? $popularPlace->status;
            $popularPlace->image_url = $image_url;
            $popularPlace->image_path = $image_path;
            $popularPlace->update();

            DB::commit();
            return $popularPlace;
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
    public function delete(PopularPlace $popularPlace)
    {
        DB::beginTransaction();
        try {
            $s3 = app(S3Service::class);

            $oldImage = $popularPlace->image_path;
            $s3->delete($oldImage);

            // Perform delete
            $deleted = $popularPlace->delete();

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
                'id' => $popularPlace->id,
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
        return PopularPlace::find($id);
    }
    public function checkExist($userId, $hotelId, $floorId)
    {
        $checkValid = Floor::where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('id', $floorId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function checkEmailExist($userId, $email)
    {
        $checkEmailExist = User::where('email', $email)
            ->where('id', '!=', $userId)
            ->exists();
        return $checkEmailExist;
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
    public function checkBookingPercentage($userId, $hotelId)
    {
        $checkBookingPercentage = Hotel::where('user_id', $userId)
            ->where('id', $hotelId)
            ->value('booking_percentage');

        return $checkBookingPercentage;
    }
}
