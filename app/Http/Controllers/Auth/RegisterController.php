<?php



namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
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
                try {
                    $response = @file_get_contents("http://ip-api.com/json/{$ipAddress}");
                    $data = $response ? json_decode($response, true) : null;

                    if ($data && $data['status'] === 'success') {
                        $lat = $data['lat'];
                        $long = $data['lon'];
                    }
                } catch (\Exception $e) {
                    // fallback if API fails
                    $lat = '';
                    $long = '';
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
                'my_refer_code'    => $myReferCode,
                'password'         => Hash::make($request->password),
                # 'status'           => 'Active',
            ]);

            // Generate API token
            $token = $user->createToken('API Token')->plainTextToken;

            DB::commit();

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
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
            // generate 6 letters + 3 digits
            $letters = Str::lower(Str::random(3)); // random 6 letters
            $numbers = random_int(100, 999);             // random 3 digit number
            $code = $letters . $numbers;

        } while (User::where('my_refer_code', $code)->exists());

        return (string) $code;
    }

}
