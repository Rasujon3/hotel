<?php

use App\Modules\Rooms\Controllers\RoomController;
use App\Modules\WithdrawalMethods\Controllers\WithdrawalMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/withdrawalMethods')->middleware(['auth:sanctum', 'owner'])->group(function () {
    Route::get('/list', [WithdrawalMethodController::class, 'index'])->name('withdrawalMethods.list'); // List data
    Route::post('/create', [RoomController::class, 'store'])->name('withdrawalMethods.store'); // Create data
    Route::get('/view/{room}', [RoomController::class, 'show'])->name('withdrawalMethods.view'); // View data
    Route::post('/update/{room}', [RoomController::class, 'update'])->name('withdrawalMethods.update'); // Update data
    Route::delete('/delete/{room}', [RoomController::class, 'destroy'])->name('withdrawalMethods.delete'); // Delete data
});
