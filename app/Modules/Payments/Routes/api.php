<?php

use App\Modules\Payments\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/payments')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/list', [PaymentController::class, 'index'])->name('payments.list'); // List data
    Route::post('/create', [PaymentController::class, 'store'])->name('payments.store'); // Create data
    Route::get('/view/{room}', [PaymentController::class, 'show'])->name('payments.view'); // View data
    Route::post('/update/{room}', [PaymentController::class, 'update'])->name('payments.update'); // Update data
    Route::delete('/delete/{room}', [PaymentController::class, 'destroy'])->name('payments.delete'); // Delete data
});
