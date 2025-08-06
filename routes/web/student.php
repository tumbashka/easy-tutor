<?php

use App\Http\Controllers\Student\BoardsController;
use App\Http\Controllers\Student\HomeworkController;
use App\Http\Controllers\Student\LessonController;
use App\Http\Controllers\Student\MessagesController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\SettingsController;
use App\Http\Controllers\Student\TeachersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::redirect('/home', '/student/lessons')->name('home');

        Route::prefix('lessons')->name('lessons.')->group(function () {
            Route::get('/', [LessonController::class, 'index'])->name('index');
        });
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
        });
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
        });
        Route::prefix('boards')->name('boards.')->group(function () {
            Route::get('/', [BoardsController::class, 'index'])->name('index');
        });
        Route::prefix('homework')->name('homework.')->group(function () {
            Route::get('/', [HomeworkController::class, 'index'])->name('index');
        });
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/', [TeachersController::class, 'index'])->name('index');
        });
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [MessagesController::class, 'index'])->name('index');
        });
    });
