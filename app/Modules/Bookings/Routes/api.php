<?php

use App\Modules\Bookings\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
/*
Route::prefix('v1/bookings')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/list', [BookingController::class, 'index'])->name('bookings.list'); // List data
    Route::post('/create', [BookingController::class, 'store'])->name('bookings.store'); // Create data
    Route::get('/view/{room}', [BookingController::class, 'show'])->name('bookings.view'); // View data
    Route::post('/update/{room}', [BookingController::class, 'update'])->name('bookings.update'); // Update data
    Route::post('/update-status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('/update-check-in-status', [BookingController::class, 'updateCheckInStatus'])->name('bookings.update-check-in-status');
    Route::post('/update-check-out-status', [BookingController::class, 'updateCheckOutStatus'])->name('bookings.update-check-out-status');
    Route::delete('/delete/{room}', [BookingController::class, 'destroy'])->name('bookings.delete'); // Delete data
});
*/
Route::prefix('v1/bookings')->middleware('auth:sanctum')->group(function () {
    // ✅ Routes accessible by both owner & receptionist
    Route::middleware('roles:owner,receptionist')->group(function () {
        Route::get('/list', [BookingController::class, 'index'])->name('bookings.list'); // List data
        Route::post('/search-booking-by-user', [BookingController::class, 'searchBookingByUser'])->name('bookings.search-booking-by-user');
        Route::post('/update-status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::post('/update-check-in-status', [BookingController::class, 'updateCheckInStatus'])->name('bookings.update-check-in-status');
        Route::post('/update-check-out-status', [BookingController::class, 'updateCheckOutStatus'])->name('bookings.update-check-out-status');
    });

    // ✅ User-only routes
    Route::middleware('roles:user')->group(function () {
        Route::post('/create', [BookingController::class, 'store'])->name('bookings.store'); // Create data
        Route::post('/user-booking-list', [BookingController::class, 'userBookings'])->name('bookings.user-bookings');
    });
});
