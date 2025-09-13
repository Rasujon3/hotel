<?php

namespace App\Modules\Receptionists\Repositories;

use App\Models\User;
use App\Modules\Expenses\Models\Expense;
use App\Modules\Expenses\Models\ExpenseImg;
use App\Modules\Floors\Models\Floor;
use App\Modules\Floors\Models\FloorImg;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Receptionists\Models\Receptionist;
use App\Services\S3Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class ReceptionistRepository
{
    public function all($userId, $hotelId)
    {
        $data = Receptionist::with('hotel')
            ->where('hotel_id', $hotelId)
            ->where('created_by', $userId)
            ->get();

        return $data;
    }

    public function delete(Receptionist $receptionist)
    {
        DB::beginTransaction();
        try {
            // 2. Get all user images
            $oldImages = $receptionist->image_path;
            if ($oldImages) {
                $s3 = app(S3Service::class);
                $s3->delete($receptionist->image_path);
            }

            // 4. Delete the image records from the database
            $receptionist->user()->delete();

            // 5. Finally, delete the floor itself
            $receptionist->delete();

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error deleting data: ' , [
                'id' => $receptionist->id,
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
        return Receptionist::find($id);
    }
    public function checkValid($userId, $hotelId)
    {
        $checkValid = Hotel::where('user_id', $userId)
            ->where('id', $hotelId)
            ->where('status', 'Active')
            ->exists();
        return $checkValid;
    }
    public function register($request, $userId)
    {
        DB::beginTransaction();
        try {
            $hotelId = $request->hotel_id;
            // Create the record in the database
            $user = $this->userRegistration($request, $hotelId);

            $receptionist = Receptionist::create([
                'user_id'    => $user->id,
                'hotel_id'   => $hotelId,
                'created_by' => $userId,
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'nid'        => $request->nid,
                'shift'      => $request->shift,
                'image_path' => $user->image_path,
                'image_url'  => $user->image_url,
            ]);

            DB::commit();

            return $receptionist;
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
    private function userRegistration($request, $hotelId)
    {
        $myReferCode = $this->generateUniqueReferCode();
        $ipAddress = $request->ip();

        $lat = $request->lat ?? '';
        $long = $request->long ?? '';

        if (empty($lat) && empty($long)) {
            $position = Location::get($ipAddress);

            if ($position) {
                $lat = $position->latitude;
                $long = $position->longitude;
            }
        }

        $now = Carbon::now();

        $day   = $now->format('d');
        $month = $now->format('M');
        $year  = $now->format('Y');

        $image_url = '';
        $image_path = '';
        if($request->hasFile('image')) {
            $s3 = app(S3Service::class);

            $file = $request->file('image');
            $result = $s3->upload($file, 'profile');

            if ($result) {
                $image_url = $result['url'];
                $image_path = $result['path'];
            }
        }
        // Create new user
        $user = User::create([
            'full_name'        => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'user_type_id'     => '4',
            'role'             => 'receptionist',
            'ip_address'       => $ipAddress,
            'lat'              => $lat,
            'long'             => $long,
            'day'              => $day,
            'month'            => $month,
            'year'             => $year,
            'fbase'            => '',
            'refer_code'       => '',
            'image_url'        => $image_url,
            'image_path'       => $image_path,
            'my_refer_code'    => $myReferCode,
            'password'         => Hash::make($request->password),
            'status'           => 'Active',
            'hotel_id'         => $hotelId
        ]);

        return $user;
    }
    private function generateUniqueReferCode()
    {
        do {
            $letters = Str::upper(Str::random(3)); // random 3 letters
            $numbers = random_int(100, 999);             // random 3 digit number
            $code = $letters . $numbers;

        } while (User::where('my_refer_code', $code)->exists());

        return (string) $code;
    }
    public function uniqueCheck($name, $email, $id)
    {
        $userId = $this->getUserId($id);
        $uniqueCheck = User::where('full_name', $name)
            ->where('email', $email)
            ->where('id', '!=', $userId)
            ->exists();

        return $uniqueCheck;
    }
    private function getUserId($id)
    {
        $userId = Receptionist::where('id', $id)->value('user_id');
        return $userId;
    }
    public function receptionistUpdate(Receptionist $receptionist, $request, $userId, $id)
    {
        DB::beginTransaction();
        try {
            // Create the record in the database
            $user = $this->userRegistrationUpdate($request, $id);

            $receptionist->updated_by = $userId;
            $receptionist->name = $request->name ?? $receptionist->name;
            $receptionist->email = $request->email ?? $receptionist->email;
            $receptionist->nid = $request->nid ?? $receptionist->nid;
            $receptionist->shift = $request->shift ?? $receptionist->shift;
            $receptionist->image_url = $user->image_url;
            $receptionist->image_path = $user->image_path;

            $receptionist->update();

            DB::commit();

            return $receptionist;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    private function userRegistrationUpdate($request, $id)
    {
        $userId = $this->getUserId($id);
        $user = User::where('id',$userId)->first();

        $image_url = $user->image_url;
        $image_path = $user->image_path;
        if($request->hasFile('image')) {
            $s3 = app(S3Service::class);

            $file = $request->file('image');
            $result = $s3->upload($file, 'profile');

            if ($result) {
                $image_url = $result['url'];
                $image_path = $result['path'];
            }
        }

        // Update user
        $user->full_name = $request->name ?? $user->full_name;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->image_url = $image_url;
        $user->image_path = $image_path;

        $user->update();

        return $user;
    }
}
