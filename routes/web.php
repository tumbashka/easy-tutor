<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonTimeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\Statistic\EarningsController;
use App\Http\Controllers\Statistic\LessonsController;
use App\Http\Controllers\StudentController;
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

});

require __DIR__ . '/auth.php';
require __DIR__ . '/statistic.php';


Route::fallback(function () {
    return view('errors.404');
});
