<?php

use App\Modules\Rooms\Controllers\RoomController;
use App\Modules\WithdrawalMethods\Controllers\WithdrawalMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/withdrawalMethods')->middleware(['auth:sanctum', 'roles:owner'])->group(function () {
    Route::get('/list', [WithdrawalMethodController::class, 'index'])->name('withdrawalMethods.list'); // List data
    Route::post('/create', [WithdrawalMethodController::class, 'store'])->name('withdrawalMethods.store'); // Create data
    Route::get('/view/{withdrawalMethod}', [WithdrawalMethodController::class, 'show'])->name('withdrawalMethods.view'); // View data
    Route::post('/update/{withdrawalMethod}', [WithdrawalMethodController::class, 'update'])->name('withdrawalMethods.update'); // Update data
    Route::post('/withdrawal-history', [WithdrawalMethodController::class, 'withdrawalHistory'])->name('withdrawalMethods.withdrawal-history'); // Create data
    Route::delete('/delete/{withdrawalMethod}', [WithdrawalMethodController::class, 'destroy'])->name('withdrawalMethods.delete'); // Delete data
});
