<?php

use App\Modules\Offers\Controllers\OfferController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/offers')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/list', [OfferController::class, 'index'])->name('offers.list'); // List data
    Route::post('/create', [OfferController::class, 'store'])->name('offers.store'); // Create data
    Route::get('/view/{offer}', [OfferController::class, 'show'])->name('offers.view'); // View data
    Route::post('/update/{offer}', [OfferController::class, 'update'])->name('offers.update'); // Update data
    Route::delete('/delete/{offer}', [OfferController::class, 'destroy'])->name('offers.delete'); // Delete data
});
