<?php

namespace App\Modules\Expenses\Repositories;

use App\Modules\Expenses\Models\Expense;
use App\Modules\Expenses\Models\ExpenseImg;
use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ExpenseRepository
{
    public function all($userId, $hotelId)
    {
        $data = Expense::with('images', 'hotel')
            ->where('hotel_id', $hotelId)
            ->where('user_id', $userId)
            ->get();

        $totalSpends = Expense::sum('amount');

        return [
            'total_spends' => $totalSpends,
            'data' =>$data
        ];
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $hotelId = $data['hotel_id'];
            // Create the record in the database
            $created = Expense::create($data);

            $s3 = app(S3Service::class);

            if (!empty($data['images'])) {
                foreach ($data['images'] as $file) {
                    $image_url = null;
                    $image_path = null;

                    $result = $s3->upload($file, 'expense');

                    if ($result) {
                        $image_url = $result['url'];
                        $image_path = $result['path'];
                    }

                    ExpenseImg::create([
                        'user_id' => $userId,
                        'hotel_id' => $hotelId,
                        'expense_id' => $created->id,
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
    public function update(Expense $expense, array $data, $userId)
    {
        DB::beginTransaction();
        try {
            // Perform the update
            $expense->update($data);

            if (!empty($data['images'])) {
                $s3 = app(S3Service::class);

                $oldImages = ExpenseImg::where('expense_id', $expense->id)->get();
                if (count($oldImages) > 0) {
                    foreach ($oldImages as $img) {
                        if ($img->image_path) {
                            $s3->delete($img->image_path);
                        }
                        $img->delete();
                    }
                }

                $hotelId = $data['hotel_id'] ?? $expense->hotel_id;
                if (!empty($data['images'])) {
                    foreach ($data['images'] as $file) {
                        $image_url = null;
                        $image_path = null;

                        $result = $s3->upload($file, 'expense');

                        if ($result) {
                            $image_url = $result['url'];
                            $image_path = $result['path'];
                        }

                        ExpenseImg::create([
                            'user_id'   => $userId,
                            'hotel_id'  => $hotelId,
                            'expense_id'  => $expense->id,
                            'image_url'   => $image_url,
                            'image_path'  => $image_path,
                        ]);
                    }
                }
            }

            DB::commit();
            // Return the updated record
            return $this->find($expense, $userId);
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
    public function delete(Expense $expense)
    {
        DB::beginTransaction();
        try {
            // 2. Get all floor images
            $oldImages = $expense->images;

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
            $expense->images()->delete();

            // 5. Finally, delete the floor itself
            $expense->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting data: ' , [
                'id' => $expense->id,
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
        return Expense::with('images', 'hotel')->find($id);
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
