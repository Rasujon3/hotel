<?php

use App\Modules\Hotels\Controllers\HotelController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1/hotels')->middleware('auth:sanctum')->group(function () {
    Route::get('/my-hotel-list', [HotelController::class, 'index'])->name('hotels.list'); // List data
    Route::post('/create', [HotelController::class, 'store'])->name('hotels.store'); // Create data
    Route::get('/view/{hotel}', [HotelController::class, 'show'])->name('hotels.view'); // View data
    Route::post('/update/{hotel}', [HotelController::class, 'update'])->name('hotels.update'); // Update data
    Route::delete('/delete/{hotel}', [HotelController::class, 'destroy'])->name('hotels.delete'); // Delete data
});
