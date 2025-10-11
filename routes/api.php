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
    Route::post('/logout', [LoginController::class, 'logout'])->name('user.logout')->middleware(['auth:sanctum']);
});
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/user-info', [RegisterController::class, 'userInfo'])->name('user.info')->middleware(['auth:sanctum', 'roles:user,owner,receptionist']);
    Route::post('/user-profile-update', [RegisterController::class, 'userProfileUpdate'])->name('user.profile.update')->middleware(['auth:sanctum', 'roles:user']);
    Route::post('/change-password', [RegisterController::class, 'changePassword'])->name('user.change-password')->middleware(['auth:sanctum', 'roles:user']);

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetRequest']); // OTP or Email
    Route::post('/verify-reset-otp', [ForgotPasswordController::class, 'verifyResetOtp']); // OTP verification
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPasswords']); // Reset via OTP
    Route::post('/password/reset', [ForgotPasswordController::class, 'resetPasswordWithToken']); // Reset via email token

    Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/auth/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
    Route::post('/auth/reset-password', [ForgotPasswordController::class, 'resetPassword']);

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

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');

    return 'All caches (config, route, application) have been cleared!';
});
