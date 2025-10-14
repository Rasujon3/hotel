<?php

namespace App\Modules\Bookings\Repositories;

use App\Models\User;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Models\BookingDetail;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Payments\Models\Payment;
use App\Modules\Rooms\Models\Room;
use App\Modules\Rooms\Models\RoomImg;
use App\Services\S3Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BookingRepository
{
    public function all($hotelId)
    {
        $data = Booking::with('bookingDetails', 'bookingDetails.room', 'bookingDetails.hotel', 'user')
            ->where('hotel_id', $hotelId)
            ->get();

        return $data;
    }
    public function searchBookingByUser($phone)
    {
        $user = User::where('phone', $phone)->first();
        $data = BookingDetail::with('room','hotel')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return [
            'user' => $user,
            'booking_details' => $data
        ];
    }
    public function store(array $data, $userId, $hotelId)
    {
        DB::beginTransaction();
        try {
            $hotel = Hotel::where('id', $hotelId)->first();

            // 1. Create booking
            $booking = Booking::create([
                'user_id' => $userId,
                'hotel_id' => $hotelId,
                # 'booking_start_date' => $data['booking_start_date'],
                # 'booking_end_date' => $data['booking_end_date'],
                # 'check_in' => $data['check_in'] ?? null,
                # 'check_out' => $data['check_out'] ?? null,
                'payment_type' => 'Online',
                'total' => $data['total'],
                'paid' => $data['payment']['amount'],
                'due' => $data['total'] - $data['paid'],
                'status' => 'confirmed',
            ]);

            // 2. Add booking details (multiple rooms)
            foreach ($data['rooms'] as $room) {
                // i. Store booking details
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'user_id' => $userId,
                    'hotel_id' => $room['hotel_id'],
                    'building_id' => $room['building_id'],
                    'floor_id' => $room['floor_id'],
                    'room_id' => $room['room_id'],
                    'booking_start_date' => $room['booking_start_date'] . $hotel->check_in_time,
                    'booking_end_date' => $room['booking_end_date'] . $hotel->check_out_time,
                    'check_in' => $room['check_in'] ?? null,
                    'check_out' => $room['check_out'] ?? null,
                    'day_count' => $room['day_count'],
                    'rent' => $room['rent'],
                    'status' => 'confirmed',
                ]);
                // ii. Update the corresponding room status and booking times
                Room::where('id', $room['room_id'])
                    ->update([
                        'start_booking_time' => $room['booking_start_date'] . " " . $hotel->check_in_time,
                        'end_booking_time'   => $room['booking_end_date'] . " " . $hotel->check_out_time,
                        'current_status'     => 'booked',
                    ]);
            }

            // 3. Save payment
            Payment::create([
                'booking_id' => $booking->id,
                'payment_type' => $data['payment']['payment_type'],
                'payment_method' => $data['payment']['payment_method'],
                'acc_no' => $data['payment']['acc_no'] ?? null,
                'amount' => $data['payment']['amount'],
                'pay_type' => 'booking',
                'transaction_id' => $data['payment']['transaction_id'],
                'reference' => $data['payment']['reference'] ?? null,
                'created_by' => $userId,
            ]);

            // 4. Balance add on hotels table
            $hotel = Hotel::where('id', $hotelId)->first();
            $calculateSysCom = $this->calculateSysCom($data['total'], $hotel->system_commission);

            $newBalance = $hotel->balance + $data['payment']['amount'] - $calculateSysCom;
            $hotel->update(['balance' => $newBalance]);

            DB::commit();
            return $booking;

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

    public function calculateSysCom($total, $sysCom)
    {
        return ceil(($total * $sysCom) / 100);
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

                $hotelId = $data['hotel_id'] ?? $room->hotel_id;
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
    public function checkExist($userId, $hotelId, $floorId)
    {
        $checkValid = Floor::where('hotel_id', $hotelId)
            ->where('id', $floorId)
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
    public function checkRoomExists($hotelId, $floorId, $roomId): array
    {
        $exists = Room::where('hotel_id', $hotelId)
            ->where('floor_id', $floorId)
            ->where('id', $roomId)
            ->where('status', 'Active')
            ->exists();

        return $exists
            ? ['status' => true]
            : ['status' => false, 'message' => 'Room not found for the selected hotel and floor.'];
    }
    public function checkRoomAvailability($roomId, $bookingStartDate): array
    {
        $room = Room::find($roomId);

        if (!$room) {
            return ['status' => false, 'message' => 'Room does not exist.'];
        }
        $roomEndBookingTime = $room->end_booking_time ? Carbon::parse($room->end_booking_time)->startOfDay() : null;

//        if ($room->end_booking_time > $bookingStartDate) {
        if ($roomEndBookingTime && $roomEndBookingTime > $bookingStartDate) {
            return ['status' => false, 'message' => "Room {$room->room_no} is currently {$room->current_status}."];
        }

        return ['status' => true];
    }
    public function checkAllRoomsSameHotel(array $rooms): array
    {
        $hotelIds = array_column($rooms, 'hotel_id');
        $uniqueHotels = array_unique($hotelIds);

        return count($uniqueHotels) === 1
            ? ['status' => true]
            : ['status' => false, 'message' => 'All selected rooms must belong to the same hotel.'];
    }
    public function checkBookingStatusAlreadyCheckedIn($bookingDetailId)
    {
        $checkValid = BookingDetail::where('id', $bookingDetailId)
            ->where('check_in', '!=', null)
            ->where('status', '!=', 'confirmed')
            ->exists();
        return $checkValid;
    }
    public function checkBookingStatusAlreadyCheckedOut($bookingDetailId)
    {
        $checkValid = BookingDetail::where('id', $bookingDetailId)
            ->where('check_out', '!=', null)
            ->where('status', '!=', 'checked_in')
            ->exists();
        return $checkValid;
    }
    public function checkDue($bookingDetailId)
    {
        $bookingId = BookingDetail::where('id', $bookingDetailId)->value('booking_id');
        $checkValid = Booking::where('id', $bookingId)
            ->where('due', '>', 0)
            ->exists();
        return $checkValid;
    }
    public function checkedInStatusUpdate(array $data, $userId, $bookingDetailId)
    {
        try {
            $booking = BookingDetail::where('id', $bookingDetailId)->first();
            $booking->check_in = now();
            $booking->status = 'checked_in';
            $booking->update();

            return $booking;

        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in checkedInStatusUpdate data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function checkedOutStatusUpdate(array $data, $userId, $bookingDetailId)
    {
        DB::beginTransaction();
        try {
            // ✅ 1. Update booking status
            $booking = BookingDetail::findOrFail($bookingDetailId);
            $booking->check_out = now();
            $booking->status = 'checked_out';
            $booking->save();

            // ✅ 2. Get all booked room IDs for this booking
            $roomIds = BookingDetail::where('id', $bookingDetailId)->pluck('room_id');

            if ($roomIds->isNotEmpty()) {
                // ✅ 3. Loop through each room to check conditions
                foreach ($roomIds as $roomId) {
                    $room = Room::find($roomId);

                    if (!$room) {
                        continue;
                    }

                    $updateData = [];

                    // ✅ Condition 1: If room.start_booking_time == booking.booking_start_date
                    if ($room->start_booking_time == $booking->booking_start_date) {
                        $updateData['start_booking_time'] = null;
                    }

                    // ✅ Condition 2: If room.end_booking_time == booking.booking_end_date
                    if ($room->end_booking_time == $booking->booking_end_date) {
                        $updateData['end_booking_time'] = null;
                    }

                    // ✅ Condition 3: If BOTH start & end match, set status = available
                    if (
                        $room->start_booking_time == $booking->booking_start_date &&
                        $room->end_booking_time == $booking->booking_end_date
                    ) {
                        $updateData['current_status'] = 'available';
                    }

                    // ✅ Only update if there are changes
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = now();
                        $room->update($updateData);
                    }
                }
            }

            DB::commit();
            return $booking;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error in checkedOutStatusUpdate:', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
    public function userBookings($userId, $bookingId, $status)
    {
        try {
            $data = Booking::with('bookingDetails', 'bookingDetails.hotel', 'bookingDetails.room')
                ->where('user_id', $userId)
                ->when($bookingId, function ($query, $bookingId) {
                    $query->where('id', $bookingId);
                })
                ->when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->get();

            return $data;

        } catch (Exception $e) {

            Log::error('Error in userBookings:', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return [];
        }
    }
}
