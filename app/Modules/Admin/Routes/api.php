<?php


use App\Modules\Admin\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/admin')->middleware(['auth:sanctum', 'roles:super_admin'])->group(function () {
    Route::get('/owner-list', [AdminController::class, 'ownerList'])->name('owner-list'); // Update User Status
    Route::get('/hotel-list', [AdminController::class, 'hotelList'])->name('owner-list'); // Update User Status
    Route::post('/owner-status-update', [AdminController::class, 'ownerStatusUpdate'])->name('owner-status-update'); // Update User Status
    Route::post('/owner-withdraw-add', [AdminController::class, 'ownerWithdrawAdd'])->name('owner-withdraw-add'); // Update User Status
});
