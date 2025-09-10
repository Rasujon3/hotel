<?php

use App\Modules\Hotels\Controllers\HotelController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1/hotels')->middleware('auth:sanctum')->group(function () {
    Route::get('/my-hotel-list', [HotelController::class, 'index'])->name('hotels.list'); // List data
    Route::get('/check-availability', [HotelController::class, 'checkAvailability'])->name('hotels.checkAvailability');  // Check availability data
    Route::get('/history', [HotelController::class, 'history'])->name('hotels.history');  // History data
    Route::post('/create', [HotelController::class, 'store'])->name('hotels.store'); // Create data
    Route::post('/import', [HotelController::class, 'import'])->name('hotels.import'); // import data
    Route::put('/bulk-update', [HotelController::class, 'bulkUpdate'])->name('hotels.bulkUpdate'); // Bulk update
    Route::get('/view/{area}', [HotelController::class, 'show'])->name('hotels.view'); // View data
    Route::put('/update/{area}', [HotelController::class, 'update'])->name('hotels.update'); // Update data
});
