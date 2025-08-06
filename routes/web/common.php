<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Teacher\FreeTimeController;
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

Route::middleware(['auth', 'verified'])->prefix('/chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/personal/{user}', [ChatController::class, 'findUserChatOrCreate'])->name('personal.find_or_create');
    Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
    Route::get('/{chat}/accept', [ChatController::class, 'accept'])->name('accept');
    Route::get('/{chat}/cancel', [ChatController::class, 'cancel'])->name('cancel');
    Route::get('/{chat}/ban', [ChatController::class, 'ban'])->name('ban');
    Route::post('/{chat}/message', [ChatController::class, 'store_message'])->name('message.store');
});
