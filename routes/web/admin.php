<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'admin', 'active'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except('show');
    Route::get('users/students', [UserController::class, 'students'])->name('users.students');
    Route::get('users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
});
