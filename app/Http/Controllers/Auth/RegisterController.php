<?php



namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RegisterController extends AppBaseController
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create new user
            $user = User::create([
                'full_name'        => $request->full_name,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'user_type_id'     => $request->user_type_id,
                'role'             => $request->role,
                'ip_address'       => $request->ip_address,
                'lat'              => $request->lat,
                'long'             => $request->long,
                'day'              => $request->day,
                'month'            => $request->month,
                'year'             => $request->year,
                'fbase'            => $request->fbase ?? '',
                'refer_code'       => $request->refer_code,
                'password'         => Hash::make($request->password),
                'token'            => $request->token ?? \Str::random(60),
                'status'           => 'Active',
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

}
