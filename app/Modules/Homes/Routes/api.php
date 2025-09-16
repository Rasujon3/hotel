<?php

use App\Modules\Homes\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/homes')->group(function () {
    Route::get('/popular-hotels', [HomeController::class, 'popularHotels'])->name('homes.popular-hotels'); // List data
    Route::get('/property-type', [HomeController::class, 'propertyType'])->name('homes.property-type'); // List data
    Route::post('/search-by-area', [HomeController::class, 'searchByArea'])->name('homes.search-by-area'); // List data
    Route::post('/hotel-details', [HomeController::class, 'hotelDetails'])->name('homes.hotel-details'); // List data
    Route::post('/room-details', [HomeController::class, 'roomDetails'])->name('homes.room-details'); // List data
    Route::post('/create', [HomeController::class, 'store'])->name('homes.store'); // Create data
    Route::get('/view/{facility}', [HomeController::class, 'show'])->name('homes.view'); // View data
    Route::post('/update/{facility}', [HomeController::class, 'update'])->name('homes.update'); // Update data
    Route::delete('/delete/{facility}', [HomeController::class, 'destroy'])->name('homes.delete'); // Delete data
});
