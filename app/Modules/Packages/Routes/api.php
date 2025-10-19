<?php

use App\Modules\Packages\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

//Route::prefix('v1/packages')->middleware(['auth:sanctum', 'roles:super_admin'])->group(function () {
Route::prefix('v1/packages')->group(function () {
    Route::get('/list', [PackageController::class, 'index'])->name('packages.list'); // List data
    Route::post('/create', [PackageController::class, 'store'])->name('packages.store'); // Create data
    Route::get('/view/{package}', [PackageController::class, 'show'])->name('packages.view'); // View data
    Route::post('/update/{package}', [PackageController::class, 'update'])->name('packages.update'); // Update data
    Route::delete('/delete/{package}', [PackageController::class, 'destroy'])->name('packages.delete'); // Delete data
});
