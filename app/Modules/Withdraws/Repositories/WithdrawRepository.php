<?php

namespace App\Modules\Withdraws\Repositories;

use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use App\Modules\Rooms\Models\RoomImg;
use App\Modules\WithdrawalMethods\Models\WithdrawalMethod;
use App\Modules\Withdraws\Models\Withdraw;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WithdrawRepository
{
    public function all()
    {
        $data = Withdraw::with('hotel')->get();

        return $data;
    }
    public function store(array $data, $userId, $hotelId)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $withdrawalMethod = WithdrawalMethod::where('hotel_id', $hotelId)->first();
            $data['title'] = $withdrawalMethod->payment_method . '_' . $withdrawalMethod->acc_no;
            $data['withdrawal_method_id'] = $withdrawalMethod->id;
            $data['created_by'] = $userId;

            // Create the data record in the database
            $created = Withdraw::create($data);

            $hotel = Hotel::where('id', $hotelId)->first();
            $hotel->balance = $hotel->balance - $data['amount'];
            $hotel->update();

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
    public function update(Withdraw $withdraw, array $data, $userId, $hotelId, $amount)
    {
        DB::beginTransaction();
        try {
            $hotel = Hotel::where('id', $hotelId)->first();
            $hotelBalance = $hotel->balance;
            $hotelBalanceUpdate = $hotel->balance;

            $prevAmount = $withdraw->amount;
            $data['amount'] = $prevAmount;

            if ($prevAmount != $amount) {
                $hotelBalanceUpdate = ($hotelBalance + $prevAmount) - $amount;
                $data['amount'] = $amount;
            }

            // Perform the update
            $withdraw->update($data);

            $hotel->balance = $hotelBalanceUpdate;
            $hotel->update();

            DB::commit();
            return $this->find($withdraw->id);
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
    public function delete(Withdraw $withdraw)
    {
        DB::beginTransaction();
        try {
            // 1. Add amount to hotel balance
            $hotel = Hotel::where('id', $withdraw->hotel_id)->first();
            $prevAmount = $withdraw->amount;

            $hotel->balance = $hotel->balance + $prevAmount;
            $hotel->update();

            // 5. Finally, delete itself
            $withdraw->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting data: ' , [
                'id' => $withdraw->id,
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
        return Withdraw::with('hotel')->find($id);
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
    public function checkBalance($hotelId, $amount)
    {
        $hotelBalance = Hotel::where('id', $hotelId)->value('balance');
        $status = $hotelBalance >= $amount;

        return $status;
    }
    public function checkWithdrawalMethodExist($hotelId)
    {
        $check = WithdrawalMethod::where('hotel_id', $hotelId)->first();

        return $check;
    }
}
