<?php

use App\Modules\Payments\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/payments')->middleware('auth:sanctum')->group(function () {
    // ✅ Routes accessible by both owner & receptionist
    Route::middleware('roles:owner,receptionist')->group(function () {
        Route::post('/due-list', [PaymentController::class, 'dueList'])->name('payments.due-list'); // List data
        Route::post('/due-search', [PaymentController::class, 'dueSearch'])->name('payments.due-search'); // List data
        Route::post('/collect-due', [PaymentController::class, 'collectDue'])->name('payments.collect-due'); // List data
    });

    // ✅ User-only routes
    Route::middleware('roles:user')->group(function () {
        Route::post('/user-collect-due', [PaymentController::class, 'userCollectDue'])->name('payments.user-collect-due');
    });
});
