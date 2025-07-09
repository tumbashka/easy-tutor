<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'admin', 'active'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except('show');
//    Route::get('users/students', [UserController::class, 'students'])->name('users.students');
//    Route::get('users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');

    Route::get('backups', [BackupController::class, 'index'])->name('backups');
    Route::get('backups/create', [BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/{dir}/{file}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{dir}/{file}/delete', [BackupController::class, 'delete'])->name('backups.delete');
});
