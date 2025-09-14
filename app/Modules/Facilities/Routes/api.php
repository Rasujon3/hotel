<?php

use App\Modules\Facilities\Controllers\FacilityController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/facilities')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/list', [FacilityController::class, 'index'])->name('facilities.list'); // List data
    Route::post('/create', [FacilityController::class, 'store'])->name('facilities.store'); // Create data
    Route::get('/view/{facility}', [FacilityController::class, 'show'])->name('facilities.view'); // View data
    Route::post('/update/{facility}', [FacilityController::class, 'update'])->name('facilities.update'); // Update data
    Route::delete('/delete/{facility}', [FacilityController::class, 'destroy'])->name('facilities.delete'); // Delete data
});
