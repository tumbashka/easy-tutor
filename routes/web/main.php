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
use App\Http\Controllers\Teacher\TeacherSettingsController;
use App\Http\Controllers\Teacher\UserController;
use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/home', '/schedule')->name('home');
Route::permanentRedirect('/', '/schedule');

Route::get('user/{user}', [UserController::class, 'show'])->name('user.show')->where('user', '[0-9]+');

Route::middleware(['auth', 'verified', 'role:teacher'])->name('user.')->prefix('/user')->group(function () {
    Route::get('/profile', [UserController::class, 'index'])->name('index');
    Route::get('/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/update', [UserController::class, 'update'])->name('update');

    Route::prefix('/settings')->name('settings.')->group(function () {
        Route::get('/', [TeacherSettingsController::class, 'index'])->name('index');
        Route::post('/store_subject', [TeacherSettingsController::class, 'subjectStore'])->name('store-subject');
        Route::delete('/subject/{subject}', [TeacherSettingsController::class, 'subjectDelete'])->name('delete-subject');
        Route::put('/subject/{subject}', [TeacherSettingsController::class, 'subjectUpdate'])->name('update-subject');
    });
});


// Route::fallback(function () {
//    abort(404);
// });

Route::get('/free-time/share/{token}', [FreeTimeController::class, 'showSharedPage'])->name('free-time.show_shared_page');

Route::middleware(['auth', 'verified', 'active', 'role:teacher'])->group(function () {
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
    Route::resource('students.account', StudentAccountController::class);
    Route::resource('students.lesson-times', LessonTimeController::class)->except(['index', 'show']);
    Route::resource('students.homeworks', HomeworkController::class)->except('index', 'show');
    Route::prefix('/free-time')->name('free-time.')->group(function () {
        Route::get('/', [FreeTimeController::class, 'index'])->name('index');
        Route::get('/create', [FreeTimeController::class, 'create'])->name('create');
        Route::post('/', [FreeTimeController::class, 'store'])->name('store');
        Route::get('/{free_time}/edit', [FreeTimeController::class, 'edit'])->name('edit');
        Route::put('/{free_time}', [FreeTimeController::class, 'update'])->name('update');
        Route::delete('/{free_time}', [FreeTimeController::class, 'delete'])->name('delete');

        Route::get('/{free_time}/set_student', [FreeTimeController::class, 'setStudent'])->name('set-student');
        Route::post('/{free_time}/set_student', [FreeTimeController::class, 'setStudentProcess'])->name('set-student-process');
        Route::post('/share/', [FreeTimeController::class, 'generateEncryptedUrl'])->name('encrypt-url');
    });

    Route::resource('tasks', TaskController::class);
    Route::resource('task_categories', TaskCategoryController::class)->except('show');

    Route::delete('delete_completed_tasks', [TaskController::class, 'delete_completed'])->name('tasks.delete-completed');
    Route::get('tasks/{task}/change_completed', [TaskController::class, 'change_completed'])->name('tasks.change-completed');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
});
