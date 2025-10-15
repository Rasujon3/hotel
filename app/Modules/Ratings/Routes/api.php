<?php

use App\Modules\Ratings\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/ratings')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [RatingController::class, 'index'])->name('ratings.list'); // List data
    // ✅ Routes accessible by both owner & receptionist
    Route::middleware('roles:owner,receptionist,user')->group(function () {
//        Route::get('/list', [RatingController::class, 'index'])->name('ratings.list'); // List data
    });

    // ✅ User-only routes
    Route::middleware('roles:user')->group(function () {
        Route::post('/create', [RatingController::class, 'store'])->name('ratings.store'); // Create data
        Route::get('/view/{facility}', [RatingController::class, 'show'])->name('ratings.view'); // View data
        Route::post('/update/{facility}', [RatingController::class, 'update'])->name('ratings.update'); // Update data
        Route::delete('/delete/{facility}', [RatingController::class, 'destroy'])->name('ratings.delete'); // Delete data
    });
});
