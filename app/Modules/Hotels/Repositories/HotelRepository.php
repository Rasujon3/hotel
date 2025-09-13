<?php

namespace App\Modules\Hotels\Repositories;


use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Hotels\Models\HotelImg;
use App\Modules\Rooms\Models\Room;
use App\Services\S3Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class HotelRepository
{
    public function all($userId)
    {
        $data = Hotel::with('images', 'package')->where('user_id',$userId)->get();
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
    public function update(Hotel $hotel, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $userId)->first();
            $user->full_name = $data['full_name'] ?? $user->full_name;
            $user->email = $data['email'] ?? $user->email;
            $user->update();

            $previousPercentage = $hotel->booking_percentage;
            $checkIn  = $data['check_in_time'] ? Carbon::parse($data['check_in_time'])->format('H:i:s') : $hotel->check_in_time;
            $checkOut = $data['check_out_time'] ? Carbon::parse($data['check_out_time'])->format('H:i:s') : $hotel->check_out_time;

            // Perform the update
            $hotel->hotel_name = $data['hotel_name'] ?? $hotel->hotel_name;
            $hotel->hotel_address = $data['hotel_address'] ?? $hotel->hotel_address;
            $hotel->hotel_description = $data['hotel_description'] ?? $hotel->hotel_description;
            $hotel->booking_percentage = $data['booking_percentage'] ?? $hotel->booking_percentage;
            $hotel->check_in_time = $checkIn;
            $hotel->check_out_time = $checkOut;
            $hotel->update();

            if (
                isset($data['booking_percentage']) &&
                $data['booking_percentage'] != $previousPercentage
            ) {
                $rooms = Room::where('hotel_id', $hotel->id)->get();

                foreach ($rooms as $room) {
                    $room->booking_price = $this->calculateBookingPrice(
                        $room->price,
                        $hotel->booking_percentage
                    );
                    $room->save();
                }
            }

            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);

                $oldImages = HotelImg::where('hotel_id', $hotel->id)->get();
                if (count($oldImages) > 0) {
                    foreach ($oldImages as $img) {
                        if ($img->image_path) {
                            $s3->delete($img->image_path);
                        }
                        $img->delete();
                    }
                }

                $hotelId = $hotel->id;
                if (!empty($data['images'])) {
                    foreach ($data['images'] as $file) {
                        $image_url = null;
                        $image_path = null;

                        $result = $s3->upload($file, 'hotel');

                        if ($result) {
                            $image_url = $result['url'];
                            $image_path = $result['path'];
                        }

                        HotelImg::create([
                            'user_id'   => $userId,
                            'hotel_id'  => $hotelId,
                            'image_url'   => $image_url,
                            'image_path'  => $image_path,
                        ]);
                    }
                }
            }

            DB::commit();
            return $hotel;
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
    public function find($id, $userId)
    {
        return Hotel::with('package', 'images')
            ->where('user_id', $userId)
            ->find($id);
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
