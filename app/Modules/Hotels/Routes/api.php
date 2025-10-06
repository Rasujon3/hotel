<?php

use App\Modules\Hotels\Controllers\HotelController;
use Illuminate\Support\Facades\Route;

/*
Route::prefix('v1/hotels')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/my-hotel-list', [HotelController::class, 'index'])->name('hotels.list'); // List data
    # Route::post('/check-balance', [HotelController::class, 'checkBalance'])->name('hotels.check-balance'); // List data
    Route::post('/create', [HotelController::class, 'store'])->name('hotels.store'); // Create data
    Route::get('/view/{hotel}', [HotelController::class, 'show'])->name('hotels.view'); // View data
    Route::post('/update/{hotel}', [HotelController::class, 'update'])->name('hotels.update'); // Update data
    # Route::post('/revenue-tracker', [HotelController::class, 'revenueTracker'])->name('hotels.revenue-tracker'); // revenue-tracker data
    Route::delete('/delete/{hotel}', [HotelController::class, 'destroy'])->name('hotels.delete'); // Delete data
});

Route::prefix('v1/hotels')->middleware(['auth:sanctum', 'roles:owner'])->group(function () {
    Route::post('/check-balance', [HotelController::class, 'checkBalance'])->name('hotels.check-balance'); // List data
    Route::post('/revenue-tracker', [HotelController::class, 'revenueTracker'])->name('hotels.revenue-tracker'); // revenue-tracker data
});
*/
Route::prefix('v1/hotels')->middleware('auth:sanctum')->group(function () {

    // ✅ Routes accessible by both owner & receptionist
    Route::middleware('roles:owner,receptionist')->group(function () {
        Route::get('/property-type-list', [HotelController::class, 'propertyTypeList'])->name('hotels.propertyTypeList'); // List data
        Route::get('/popular-place-list', [HotelController::class, 'popularPlaceList'])->name('hotels.propertyTypeList'); // List data
        Route::get('/package-list', [HotelController::class, 'packageList'])->name('hotels.packageList'); // List data
        Route::get('/my-hotel-list', [HotelController::class, 'index'])->name('hotels.list'); // List data
        Route::post('/create', [HotelController::class, 'store'])->name('hotels.store'); // Create data
        Route::get('/view/{hotel}', [HotelController::class, 'show'])->name('hotels.view'); // View data
        Route::post('/update/{hotel}', [HotelController::class, 'update'])->name('hotels.update'); // Update data
        Route::delete('/delete/{hotel}', [HotelController::class, 'destroy'])->name('hotels.delete'); // Delete data
    });

    // ✅ Owner-only routes
    Route::middleware('roles:owner')->group(function () {
        Route::post('/check-balance', [HotelController::class, 'checkBalance'])->name('hotels.check-balance'); // List data
        Route::post('/revenue-tracker', [HotelController::class, 'revenueTracker'])->name('hotels.revenue-tracker'); // revenue-tracker data
    });
});
