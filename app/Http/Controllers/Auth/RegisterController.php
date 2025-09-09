<?php



namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Stevebauman\Location\Facades\Location;

class RegisterController extends AppBaseController
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
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

            // Create new user
            $user = User::create([
                'full_name'        => $request->full_name,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'user_type_id'     => $request->user_type_id,
                'role'             => $request->role,
                'ip_address'       => $ipAddress,
                'lat'              => $lat,
                'long'             => $long,
                'day'              => $day,
                'month'            => $month,
                'year'             => $year,
                'fbase'            => $request->fbase ?? '',
                'refer_code'       => $request->refer_code ?? '',
                'my_refer_code'    => $myReferCode,
                'password'         => Hash::make($request->password),
                'status'           => ($request->user_type_id === '2' && $request->role === 'user')
                                        ? 'Active'
                                        : 'Inactive',
            ]);

            // ✅ If owner, also create hotel record
            if ($request->user_type_id == 3 && $request->role === 'owner') {
                Hotel::create([
                    'user_id'          => $user->id,
                    'hotel_name'       => $request->hotel_name,
                    'hotel_description'=> $request->hotel_description,
                    'hotel_address'    => $request->hotel_address,
                    'lat'              => $lat,
                    'long'             => $long,
                    'status'           => 'Inactive',
                ]);
            }

            // Generate API token
            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return $this->sendResponse([
                'token' => $token,
                'user' => $user,
            ], 'User created successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in updating Register: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!!!',
            ], 500);
        }
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

}
