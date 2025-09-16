<?php

use App\Modules\Withdraws\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/withdraws')->middleware(['auth:sanctum', 'roles:super_admin'])->group(function () {
    Route::get('/list', [WithdrawController::class, 'index'])->name('withdraws.list'); // List data
    Route::post('/create', [WithdrawController::class, 'store'])->name('withdraws.store'); // Create data
    Route::get('/view/{withdraw}', [WithdrawController::class, 'show'])->name('withdraws.view'); // View data
    Route::post('/update/{withdraw}', [WithdrawController::class, 'update'])->name('withdraws.update'); // Update data
    Route::delete('/delete/{withdraw}', [WithdrawController::class, 'destroy'])->name('withdraws.delete'); // Delete data
});
