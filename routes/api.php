<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::prefix('/v1')->middleware('api')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [LoginController::class, 'logout']);
});
    Route::post('/register', [RegisterController::class, 'register']);

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetRequest']); // OTP or Email
    Route::post('/verify-reset-otp', [ForgotPasswordController::class, 'verifyResetOtp']); // OTP verification
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']); // Reset via OTP
    Route::post('/password/reset', [ForgotPasswordController::class, 'resetPasswordWithToken']); // Reset via email token
});

Route::get('/migrate', function(){
    Artisan::call('migrate', [
        '--force' => true,
    ]);
    return response()->json(['message' => 'Migrations run successfully.']);
});

Route::get('/db-seed', function(){
    Artisan::call('db:seed', [
        '--force' => true,
    ]);
    return response()->json(['message' => 'Database seeded successfully.']);
});
