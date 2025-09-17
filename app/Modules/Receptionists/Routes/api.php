<?php

use App\Modules\Receptionists\Controllers\ReceptionistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/receptionists')->middleware(['auth:sanctum', 'roles:owner'])->group(function () {
    Route::get('/list', [ReceptionistController::class, 'index'])->name('receptionists.list'); // List data
    Route::post('/register', [ReceptionistController::class, 'register'])->name('receptionists.register'); // Delete data
    Route::post('/update/{receptionist}', [ReceptionistController::class, 'updateReceptionist'])->name('receptionists.updateReceptionist');
    Route::delete('/delete/{receptionist}', [ReceptionistController::class, 'destroy'])->name('receptionists.delete'); // Delete data
});
