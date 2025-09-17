<?php

use App\Modules\Rooms\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/rooms')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/list', [RoomController::class, 'index'])->name('rooms.list'); // List data
    Route::post('/create', [RoomController::class, 'store'])->name('rooms.store'); // Create data
    Route::get('/view/{room}', [RoomController::class, 'show'])->name('rooms.view'); // View data
    Route::post('/update/{room}', [RoomController::class, 'update'])->name('rooms.update'); // Update data
    Route::delete('/delete/{room}', [RoomController::class, 'destroy'])->name('rooms.delete'); // Delete data
});
