<?php

use App\Modules\Floors\Controllers\FloorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/floors')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/list', [FloorController::class, 'index'])->name('floors.list'); // List data
    Route::post('/create', [FloorController::class, 'store'])->name('floors.store'); // Create data
    Route::get('/view/{floor}', [FloorController::class, 'show'])->name('floors.view'); // View data
    Route::post('/update/{floor}', [FloorController::class, 'update'])->name('floors.update'); // Update data
    Route::delete('/delete/{floor}', [FloorController::class, 'destroy'])->name('floors.delete'); // Delete data
});
