<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Teacher\FreeTimeController;
use App\Http\Controllers\Teacher\HomeworkController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Teacher\LessonTimeController;
use App\Http\Controllers\Teacher\StudentAccountController;
use App\Http\Controllers\Teacher\StudentController;
use App\Http\Controllers\Teacher\TaskCategoryController;
use App\Http\Controllers\Teacher\TaskController;
use App\Http\Controllers\Teacher\SettingsController;
use App\Http\Controllers\Teacher\UserController;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/home', '/schedule')->name('home');
Route::permanentRedirect('/', '/schedule');

Route::get('user/{user}', [UserController::class, 'show'])->name('user.show')->where('user', '[0-9]+');
Route::get('/free-time/shared/{token}', [FreeTimeController::class, 'showSharedPage'])->name('free-time.show_shared_page');

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat/user/{}', [ChatController::class, 'index'])->name('notifications.index');
});
