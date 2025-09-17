<?php

use App\Modules\Expenses\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/expenses')->middleware(['auth:sanctum', 'roles:owner,receptionist'])->group(function () {
    Route::get('/list', [ExpenseController::class, 'index'])->name('expenses.list'); // List data
    Route::post('/create', [ExpenseController::class, 'store'])->name('expenses.store'); // Create data
    Route::get('/view/{expense}', [ExpenseController::class, 'show'])->name('expenses.view'); // View data
    Route::post('/update/{expense}', [ExpenseController::class, 'update'])->name('expenses.update'); // Update data
    Route::delete('/delete/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.delete'); // Delete data
});
