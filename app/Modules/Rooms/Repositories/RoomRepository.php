<?php

namespace App\Modules\Rooms\Repositories;

use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use App\Modules\Rooms\Models\RoomImg;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RoomRepository
{
    public function all($userId, $hotelId, $floorId, $buildingId)
    {
        $data = Room::with('images','hotel', 'floor')
            ->where('building_id', $buildingId)
            ->where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId, $checkSystemCommission)
    {
        DB::beginTransaction();
        try {
            $roomPrice = $data['room_price'];
            $discount = $data['discount'];

            $rent = $this->calculateRentPrice($roomPrice, $discount);
            $data['rent'] = $rent;

            $discountAmount = $this->calculateDiscountAmount($roomPrice, $discount);
            $data['discount_amount'] = $discountAmount;

            $data['system_commission'] = $checkSystemCommission;

            $hotelId = $data['hotel_id'];
            $floorId = $data['floor_id'];
            $bookingPercentage = $this->checkBookingPercentage($hotelId);

            $data['user_id'] = $userId;
            $data['created_by'] = $userId;
            $data['booking_price'] = calculateBookingPrice($rent, $bookingPercentage);

            // icon
            $data['icon'] = null;
            $iconUrl = getBedIcon($data['bed_type'], $data['num_of_beds']);
            if ($iconUrl) {
                $data['icon'] = $iconUrl;
            }

            // Create the data record in the database
            $created = Room::create($data);

            // img upload
            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);
                foreach ($data['images'] as $file) {
                    $image_url = null;
                    $image_path = null;

                    $result = $s3->upload($file, 'room');

                    if ($result) {
                        $image_url = $result['url'];
                        $image_path = $result['path'];
                    }

                    RoomImg::create([
                        'user_id' => $userId,
                        'hotel_id' => $hotelId,
                        'floor_id' => $floorId,
                        'room_id' => $created->id,
                        'image_url'  => $image_url,
                        'image_path' => $image_path,
                    ]);
                }
            }

            DB::commit();

            return $created;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return null;
        }
    }
    public function update(Room $room, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['updated_by'] = $userId;

            $roomPrice = $data['room_price'];
            $discount = $data['discount'];

            $rent = $this->calculateRentPrice($roomPrice, $discount);
            $data['rent'] = $rent;

            $discountAmount = $this->calculateDiscountAmount($roomPrice, $discount);
            $data['discount_amount'] = $discountAmount;

            $hotelId = $data['hotel_id'];
            $floorId = $data['floor_id'];
            $bookingPercentage = $this->checkBookingPercentage($hotelId);

            $data['booking_price'] = calculateBookingPrice($rent, $bookingPercentage);

            // icon
            $data['icon'] = null;
            $iconUrl = getBedIcon($data['bed_type'], $data['num_of_beds']);
            if ($iconUrl) {
                $data['icon'] = $iconUrl;
            }
            // Perform the update
            $room->update($data);

            // img update
            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);

                $oldImages = RoomImg::where('room_id', $room->id)->get();
                if (count($oldImages) > 0) {
                    foreach ($oldImages as $img) {
                        if ($img->image_path) {
                            $s3->delete($img->image_path);
                        }
                        $img->delete();
                    }
                }

                # $hotelId = $data['hotel_id'] ?? $floor->hotel_id;
                if (!empty($data['images'])) {
                    foreach ($data['images'] as $file) {
                        $image_url = null;
                        $image_path = null;

                        $result = $s3->upload($file, 'floor');

                        if ($result) {
                            $image_url = $result['url'];
                            $image_path = $result['path'];
                        }

                        RoomImg::create([
                            'user_id' => $userId,
                            'hotel_id' => $hotelId,
                            'floor_id' => $floorId,
                            'room_id' => $room->id,
                            'image_url'  => $image_url,
                            'image_path' => $image_path,
                        ]);
                    }
                }
            }

            DB::commit();
            return $this->find($room->id);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return null;
        }
    }
    public function delete(Room $room)
    {
        DB::beginTransaction();
        try {
            // 2. Get all floor images
            $oldImages = $room->images; // Use the relationship property to get the collection

            // 3. Delete images from S3
            if ($oldImages->isNotEmpty()) {
                $s3 = app(S3Service::class);
                foreach ($oldImages as $img) {
                    if ($img->image_path) {
                        $s3->delete($img->image_path);
                    }
                }
            }

            // 4. Delete the image records from the database
            $room->images()->delete();

            // 5. Finally, delete itself
            $room->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting data: ' , [
                'id' => $room->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Room::with('images','hotel', 'building', 'floor')->find($id);
    }
    public function checkExist($hotelId, $floorId, $buildingId)
    {
        $checkValid = Floor::where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
            ->where('id', $floorId)
            ->exists();
        return $checkValid;
    }
    public function checkNameExist($hotelId, $floorId, $roomNo, $buildingId)
    {
        $checkNameExist = Room::where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $hotelId,$floorId,$roomNo, $buildingId)
    {
        $checkNameExist = Room::where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
            ->where('floor_id', $floorId)
            ->where('room_no', $roomNo)
            ->where('id', '!=', $id)
            ->exists();

        return $checkNameExist;
    }
    public function checkBookingPercentage($hotelId)
    {
        $checkBookingPercentage = Hotel::where('id', $hotelId)
            ->value('booking_percentage');

        return $checkBookingPercentage;
    }
    public function checkSystemCommission($hotelId)
    {
        $checkSystemCommission = Hotel::where('id', $hotelId)->value('system_commission');
        return $checkSystemCommission > 0;
    }
    private function calculateRentPrice($roomPrice, $discount)
    {
        $rent = $roomPrice;
        if (!empty($discount) && is_numeric($discount) && $discount > 0) {
            $rent = $roomPrice - (($roomPrice * $discount) / 100);
        }
        return $rent;
    }
    private function calculateDiscountAmount($roomPrice, $discount)
    {
        $discountAmount = 0;
        if (!empty($discount) && is_numeric($discount) && $discount > 0) {
            $discountAmount = ($roomPrice * $discount) / 100;
        }
        return $discountAmount;
    }
}
