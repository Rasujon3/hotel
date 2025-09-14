<?php

use App\Modules\Bookings\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/bookings')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/list', [BookingController::class, 'index'])->name('bookings.list'); // List data
    Route::post('/create', [BookingController::class, 'store'])->name('bookings.store'); // Create data
    Route::get('/view/{room}', [BookingController::class, 'show'])->name('bookings.view'); // View data
    Route::post('/update/{room}', [BookingController::class, 'update'])->name('bookings.update'); // Update data
    Route::delete('/delete/{room}', [BookingController::class, 'destroy'])->name('bookings.delete'); // Delete data
});
