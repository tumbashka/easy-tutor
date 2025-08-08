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
    Route::get('/count-unread', [ChatController::class, 'countUnread'])->name('countUnread');
    Route::get('/personal/{user}', [ChatController::class, 'findUserChatOrCreate'])->name('personal.find_or_create');
    Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
    Route::get('/{chat}/accept', [ChatController::class, 'accept'])->name('accept');
    Route::get('/{chat}/cancel', [ChatController::class, 'cancel'])->name('cancel');
    Route::get('/{chat}/ban', [ChatController::class, 'ban'])->name('ban');
    Route::get('/{chat}/message/{message}/reads', [ChatController::class, 'getMessageReads'])->name('message.reads');
    Route::get('/{chat}/unread-count', [ChatController::class, 'countUnreadMessages'])->name('unread-count');
    Route::post('/{chat}/message', [ChatController::class, 'storeMessage'])->name('message.store');
    Route::post('/{chat}/message/{message}/read', [ChatController::class, 'make_read'])->name('message.make_read');
    Route::get('/{chat}/load-more', [ChatController::class, 'loadMoreMessages'])->name('loadMore');
});
