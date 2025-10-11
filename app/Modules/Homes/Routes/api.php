<?php

use App\Modules\Homes\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/homes')->group(function () {
    Route::get('/popular-hotels-images', [HomeController::class, 'popularHotelImages'])->name('homes.popular-hotels'); // List data
    Route::get('/popular-hotels', [HomeController::class, 'popularHotels'])->name('homes.popular-hotels'); // List data
    Route::get('/property-type', [HomeController::class, 'propertyType'])->name('homes.property-type'); // List data
    Route::post('/search-by-area', [HomeController::class, 'searchByArea'])->name('homes.search-by-area'); // List data
    Route::post('/hotel-details', [HomeController::class, 'hotelDetails'])->name('homes.hotel-details'); // List data
    Route::post('/room-details', [HomeController::class, 'roomDetails'])->name('homes.room-details'); // List data
    Route::get('/popular-places', [HomeController::class, 'PopularPlaces'])->name('homes.popular-places'); // List data
    Route::post('/hotels-by-popular-place', [HomeController::class, 'hotelsByPopularPlace'])->name('homes.hotels-by-popular-place'); // List data
    Route::get('/weekly-offers', [HomeController::class, 'weeklyOffer'])->name('homes.weekly-offers'); // List data
    Route::post('/hotel-by-property-type', [HomeController::class, 'hotelByPropertyType'])->name('homes.hotel-by-property-type');
    # Route::post('/create', [HomeController::class, 'store'])->name('homes.store'); // Create data
    # Route::get('/view/{facility}', [HomeController::class, 'show'])->name('homes.view'); // View data
    # Route::post('/update/{facility}', [HomeController::class, 'update'])->name('homes.update'); // Update data
    # Route::delete('/delete/{facility}', [HomeController::class, 'destroy'])->name('homes.delete'); // Delete data
});
