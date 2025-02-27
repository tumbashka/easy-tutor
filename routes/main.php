<?php

use App\Http\Controllers\FreeTimeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonTimeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::permanentRedirect('/home', '/schedule')->name('home');
Route::permanentRedirect('/', '/schedule');

Route::middleware('auth')->name('user.')->group(function () {
    Route::get('user/profile', [UserController::class, 'index'])->name('index');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('user/{user}/update', [UserController::class, 'update'])->name('update');
});

Route::get('user/{user}', [UserController::class, 'show'])->name('user.show');

Route::fallback(function () {
    abort(404);
});

Route::get('/free-time/share/{token}', [FreeTimeController::class, 'show_shared_page'])->name('free-time.show_shared_page');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::prefix('/schedule')->name('schedule.')->group(function () {
        Route::get('/', [LessonController::class, 'index'])->name('index');
        Route::get('/{day}/show', [LessonController::class, 'show'])->name('show');
        Route::get('/{day}/create', [LessonController::class, 'create'])->name('lesson.create');
        Route::post('/{day}', [LessonController::class, 'store'])->name('lesson.store');
        Route::get('/{day}/{lesson}/edit', [LessonController::class, 'edit'])->name('lesson.edit');
        Route::put('/{day}/{lesson}', [LessonController::class, 'update'])->name('lesson.update');
        Route::get('/{day}/{lesson}/change_status', [LessonController::class, 'change_status'])->name('lesson.change_status');
    });

    Route::resource('students', StudentController::class);
    Route::resource('students.lesson-times', LessonTimeController::class)->except(['index', 'show']);

    Route::prefix('/free-time')->name('free-time.')->group(function () {
        Route::get('/', [FreeTimeController::class, 'index'])->name('index');
        Route::get('/create', [FreeTimeController::class, 'create'])->name('create');
        Route::post('/', [FreeTimeController::class, 'store'])->name('store');
        Route::get('/{free_time}/edit', [FreeTimeController::class, 'edit'])->name('edit');
        Route::put('/{free_time}', [FreeTimeController::class, 'update'])->name('update');
        Route::delete('/{free_time}', [FreeTimeController::class, 'delete'])->name('delete');

        Route::get('/{free_time}/set_student', [FreeTimeController::class, 'set_student'])->name('set-student');
        Route::post('/{free_time}/set_student', [FreeTimeController::class, 'set_student_process'])->name('set-student-process');
        Route::post('/share/', [FreeTimeController::class, 'generate_encrypted_url'])->name('encrypt-url');
    });

    Route::resource('tasks', TaskController::class);
    Route::resource('task_categories', TaskCategoryController::class)->except('show');

    Route::get('tasks/{task}/change_completed', [TaskController::class, 'change_completed'])->name('tasks.change-completed');
});


