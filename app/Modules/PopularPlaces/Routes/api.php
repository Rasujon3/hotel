<?php

use App\Modules\PopularPlaces\Controllers\PopularPlaceController;
use Illuminate\Support\Facades\Route;

/*
Route::prefix('v1/popularPlaces')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/my-hotel-list', [PopularPlaceController::class, 'index'])->name('popularPlaces.list'); // List data
    # Route::post('/check-balance', [PopularPlaceController::class, 'checkBalance'])->name('popularPlaces.check-balance'); // List data
    Route::post('/create', [PopularPlaceController::class, 'store'])->name('popularPlaces.store'); // Create data
    Route::get('/view/{hotel}', [PopularPlaceController::class, 'show'])->name('popularPlaces.view'); // View data
    Route::post('/update/{hotel}', [PopularPlaceController::class, 'update'])->name('popularPlaces.update'); // Update data
    # Route::post('/revenue-tracker', [PopularPlaceController::class, 'revenueTracker'])->name('popularPlaces.revenue-tracker'); // revenue-tracker data
    Route::delete('/delete/{hotel}', [PopularPlaceController::class, 'destroy'])->name('popularPlaces.delete'); // Delete data
});

Route::prefix('v1/popularPlaces')->middleware(['auth:sanctum', 'roles:owner'])->group(function () {
    Route::post('/check-balance', [PopularPlaceController::class, 'checkBalance'])->name('popularPlaces.check-balance'); // List data
    Route::post('/revenue-tracker', [PopularPlaceController::class, 'revenueTracker'])->name('popularPlaces.revenue-tracker'); // revenue-tracker data
});
*/
Route::prefix('v1/popularPlaces')->middleware('auth:sanctum')->group(function () {

    // ✅ Routes accessible by both owner & receptionist
    Route::middleware('roles:super_admin')->group(function () {
        Route::get('/list', [PopularPlaceController::class, 'index'])->name('popularPlaces.list'); // List data
        Route::post('/create', [PopularPlaceController::class, 'store'])->name('popularPlaces.store'); // Create data
        Route::get('/view/{popularPlace}', [PopularPlaceController::class, 'show'])->name('popularPlaces.view'); // View data
        Route::post('/update/{popularPlace}', [PopularPlaceController::class, 'update'])->name('popularPlaces.update'); // Update data
        Route::delete('/delete/{popularPlace}', [PopularPlaceController::class, 'destroy'])->name('popularPlaces.delete'); // Delete data
    });

    // ✅ Owner-only routes
    Route::middleware('roles:owner')->group(function () {
        Route::post('/check-balance', [PopularPlaceController::class, 'checkBalance'])->name('popularPlaces.check-balance'); // List data
        Route::post('/revenue-tracker', [PopularPlaceController::class, 'revenueTracker'])->name('popularPlaces.revenue-tracker'); // revenue-tracker data
    });
});
