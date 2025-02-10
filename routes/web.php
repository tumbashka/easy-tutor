<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonTimeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;


Route::permanentRedirect('/home', '/schedule')->name('home');
Route::permanentRedirect('/', '/schedule');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('/schedule')->name('schedule.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/{day}/show', [ScheduleController::class, 'show'])->name('show');
        Route::get('/{day}/create', [LessonController::class, 'create'])->name('lesson.create');
        Route::post('/{day}', [LessonController::class, 'store'])->name('lesson.store');
        Route::get('/{day}/{lesson}/edit', [LessonController::class, 'edit'])->name('lesson.edit');
        Route::put('/{day}/{lesson}', [LessonController::class, 'update'])->name('lesson.update');
        Route::get('/{day}/{lesson}/change', [LessonController::class, 'change'])->name('lesson.change');
    });

    Route::prefix('/students')->name('student.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/{student}/show', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'delete'])->name('delete');
        Route::get('/{student}/lesson-times/create', [LessonTimeController::class, 'create'])->name('lesson-time.create');
        Route::post('/{student}/lesson-times/', [LessonTimeController::class, 'store'])->name('lesson-time.store');
        Route::get('/{student}/lesson-times/{lessonTime}/edit', [LessonTimeController::class, 'edit'])->name('lesson-time.edit');
        Route::put('/{student}/lesson-times/{lessonTime}', [LessonTimeController::class, 'update'])->name('lesson-time.update');
        Route::delete('/{student}/lesson-times/{lessonTime}', [LessonTimeController::class, 'delete'])->name('lesson-time.delete');
    });

    Route::prefix('/statistic')->name('statistic.')->group(function () {
        Route::get('/earnings/period', [StatisticController::class, 'earnings_period'])->name('earnings.period');
        Route::post('/earnings/period', [StatisticController::class, 'calculate_earnings_period'])->name('earnings.period.calculate');
        Route::get('/earnings/students', [StatisticController::class, 'earnings_students'])->name('earnings.students');
        Route::post('/earnings/students', [StatisticController::class, 'calculate_students_period'])->name('earnings.students.calculate');
        Route::get('/lessons', [StatisticController::class, 'lessons'])->name('lessons');
    });


});

require __DIR__ . '/auth.php';


Route::fallback(function () {
    return view('errors.404');
});
