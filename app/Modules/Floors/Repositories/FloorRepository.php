<?php

namespace App\Modules\Floors\Repositories;

use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FloorRepository
{
    public function all($userId, $hotelId, $buildingId)
    {
        $data = Floor::with('images')
            ->where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
            ->get();

        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $data['created_by'] = $userId;
            $hotelId = $data['hotel_id'];

            // Create the record in the database
            $created = Floor::create($data);

            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);
                foreach ($data['images'] as $file) {
                    $image_url = null;
                    $image_path = null;

                    $result = $s3->upload($file, 'floor');

                    if ($result) {
                        $image_url = $result['url'];
                        $image_path = $result['path'];
                    }

                    FloorImg::create([
                        'user_id' => $userId,
                        'hotel_id' => $hotelId,
                        'floor_id' => $created->id,
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
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function update(Floor $floor, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['updated_by'] = $userId;

            // Perform the update
            $floor->update($data);

            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);

                $oldImages = FloorImg::where('floor_id', $floor->id)->get();
                if (count($oldImages) > 0) {
                    foreach ($oldImages as $img) {
                        if ($img->image_path) {
                            $s3->delete($img->image_path);
                        }
                        $img->delete();
                    }
                }

                $hotelId = $data['hotel_id'] ?? $floor->hotel_id;
                if (!empty($data['images'])) {
                    foreach ($data['images'] as $file) {
                        $image_url = null;
                        $image_path = null;

                        $result = $s3->upload($file, 'floor');

                        if ($result) {
                            $image_url = $result['url'];
                            $image_path = $result['path'];
                        }

                        FloorImg::create([
                            'user_id'   => $userId,
                            'hotel_id'  => $hotelId,
                            'floor_id'  => $floor->id,
                            'image_url'   => $image_url,
                            'image_path'  => $image_path,
                        ]);
                    }
                }
            }

            DB::commit();
            return $this->find($floor);
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
    public function delete(Floor $floor)
    {
        DB::beginTransaction();
        try {
            // 1. Delete related rooms first
            $floor->rooms()->delete();

            // 2. Get all floor images
            $oldImages = $floor->images; // Use the relationship property to get the collection

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
            $floor->images()->delete();

            // 5. Finally, delete the floor itself
            $floor->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

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
    public function find($id)
    {
        return Floor::with('images')->find($id);
    }
    public function checkValid($userId, $hotelId)
    {
        $checkValid = Hotel::where('user_id', $userId)
            ->where('id', $hotelId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function checkNameExist($hotelId, $name, $buildingId)
    {
        $checkNameExist = Floor::where('building_id', $buildingId)
            ->where('hotel_id', $hotelId)
            ->where('name', $name)
            ->exists();
        return $checkNameExist;
    }
    public function checkNameUpdateExist($id, $hotelId, $name, $buildingId)
    {
        $checkNameExist = Floor::where('hotel_id', $hotelId)
            ->where('building_id', $buildingId)
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
