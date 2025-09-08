<?php

use App\Modules\Hotels\Controllers\HotelController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1/hotels')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [HotelController::class, 'index'])->name('areas.list'); // List data
    Route::get('/check-availability', [HotelController::class, 'checkAvailability'])->name('areas.checkAvailability');  // Check availability data
    Route::get('/history', [HotelController::class, 'history'])->name('areas.history');  // History data
    Route::post('/create', [HotelController::class, 'store'])->name('areas.store'); // Create data
    Route::post('/import', [HotelController::class, 'import'])->name('areas.import'); // import data
    Route::put('/bulk-update', [HotelController::class, 'bulkUpdate'])->name('areas.bulkUpdate'); // Bulk update
    Route::get('/view/{area}', [HotelController::class, 'show'])->name('areas.view'); // View data
    Route::put('/update/{area}', [HotelController::class, 'update'])->name('areas.update'); // Update data
});
