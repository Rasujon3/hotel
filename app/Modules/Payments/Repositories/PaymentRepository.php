<?php

namespace App\Modules\Payments\Repositories;

use App\Modules\Bookings\Models\Booking;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Payments\Models\Payment;
use App\Modules\Rooms\Models\Room;
use App\Modules\Rooms\Models\RoomImg;
use App\Services\S3Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentRepository
{
    /*
    public function dueList($hotelId)
    {
        $data = Booking::with('user')
            ->where('hotel_id', $hotelId)
            ->where('due', '>=', 0)
            ->get();

        return $data;
    }
    */
    public function dueList($hotelId, $phone)
    {
        $data = Booking::with('user')
            ->where('hotel_id', $hotelId)
            ->where('due', '>', 0)
            ->when($phone, function ($query, $phone) {
                $query->whereHas('user', function ($q) use ($phone) {
                    $q->where('phone', $phone);
                });
            })
            ->get();

        return $data;
    }
    public function dueSearch($hotelId, $phone)
    {
        $data = Booking::with('user')
            ->where('hotel_id', $hotelId)
            ->where('due', '>', 0)
            ->whereHas('user', function ($query) use ($phone) {
                $query->where('phone', $phone);
            })
            ->first();

        return $data;
    }
    public function collectDue($bookingId, $hotelId, $amount, $userId)
    {
        DB::beginTransaction();
        try {
            // 1. booking amount update
            $booking = Booking::where('id', $bookingId)
                #->where('user_id', $userId)
                ->where('hotel_id', $hotelId)
                ->first();

            $calculateDue = $booking->due - $amount;
            $due = $calculateDue <= 0 ? 0 : $calculateDue;
            $paid = $booking->paid + $amount;

            $booking->update([
                'due' => $due,
                'paid' => $paid,
            ]);

            // 2. Save payment
            Payment::create([
                'booking_id' => $bookingId,
                'payment_type' => 'Offline',
                'payment_method' => 'cash',
                'acc_no' => null,
                'amount' => $amount,
                'pay_type' => 'additional',
                'transaction_id' => null,
                'reference' => null,
                'created_by' => $userId,
            ]);


            DB::commit();
            /*
            $message1 = "You Collected BDT {$amount} By Cash";
            $message2 = "Less Amount BDT {$due}";
            $message3 = ".";
            $message = $message1 . $due > 0 ? $message2 : $message3;
            */
            return $this->findBooking($booking->id);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in collectDue data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            # return $e->getMessage();
            return null;
        }
    }
    public function userCollectDue(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            // 1. booking amount update
            $booking = Booking::where('id', $data['booking_id'])
                #->where('user_id', $userId)
                ->where('hotel_id', $data['hotel_id'])
                ->first();

            $calculateDue = $booking->due - $data['amount'];
            $due = $calculateDue <= 0 ? 0 : $calculateDue;
            $paid = $booking->paid + $data['amount'];

            $booking->update([
                'due' => $due,
                'paid' => $paid,
            ]);

            // 2. Save payment
            Payment::create([
                'booking_id' => $data['booking_id'],
                'payment_type' => $data['payment_type'],
                'payment_method' => $data['payment_method'],
                'acc_no' => $data['payment_method'],
                'amount' => $data['amount'],
                'pay_type' => $data['pay_type'],
                'transaction_id' => $data['transaction_id'],
                'reference' => $data['reference'],
                'created_by' => $userId,
            ]);

            // 4. Balance add on hotels table
            $hotel = Hotel::where('id', $data['hotel_id'])->first();
            $calculateSysCom = $this->calculateSysCom($data['amount'], $hotel->system_commission);

            $newBalance = $hotel->balance + $data['amount'] - $calculateSysCom;
            $hotel->update(['balance' => $newBalance]);

            DB::commit();
            /*
            $message1 = "You Collected BDT {$amount} By Cash";
            $message2 = "Less Amount BDT {$due}";
            $message3 = ".";
            $message = $message1 . $due > 0 ? $message2 : $message3;
            */
            return $this->findBooking($booking->id);
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in userCollectDue data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            # return $e->getMessage();
            return null;
        }
    }
    public function calculateSysCom($total, $sysCom)
    {
        return ceil(($total * $sysCom) / 100);
    }

    public function findBooking($id)
    {
        $data = Booking::with('payments','hotel')->find($id);
        return $data;
    }
    public function store(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $rent = $data['rent'];
            $hotelId = $data['hotel_id'];
            $floorId = $data['floor_id'];
            $bookingPercentage = $this->checkBookingPercentage($userId, $hotelId);

            $data['user_id'] = $userId;
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
            $rent = $data['rent'] ?? $room->rent;
            $hotelId = $data['hotel_id'];
            $floorId = $data['floor_id'];
            $bookingPercentage = $this->checkBookingPercentage($userId, $hotelId);

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
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Room::with('images','hotel', 'floor')->find($id);
    }
    public function checkExist($hotelId)
    {
        $checkValid = Hotel::where('id', $hotelId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function checkDueZero($bookingId, $hotelId)
    {
        $checkDueZero = Booking::where('id', $bookingId)
            # ->where('user_id', $userId)
            ->where('hotel_id', $hotelId)
            ->where('due', '<=', 0)
            ->exists();
        return $checkDueZero;
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
