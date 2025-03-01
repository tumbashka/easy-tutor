<?php

use App\Http\Controllers\Statistic\EarningsController;
use App\Http\Controllers\Statistic\LessonsController;
use App\Http\Controllers\Statistic\TimeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::prefix('/statistic')->name('statistic.')->group(function () {
        Route::prefix('/earnings')->name('earnings.')->group(function () {
            Route::get('/period', [EarningsController::class, 'period'])->name('period');
            Route::post('/period', [EarningsController::class, 'period_calculate'])->name('period_calculate');
            Route::get('/students', [EarningsController::class, 'students'])->name('students');
            Route::post('/students', [EarningsController::class, 'students_calculate'])->name('students_calculate');
        });

        Route::prefix('/lessons')->name('lessons.')->group(function () {
            Route::get('/period', [LessonsController::class, 'period'])->name('period');
            Route::post('/period', [LessonsController::class, 'period_calculate'])->name('period_calculate');
            Route::get('/students', [LessonsController::class, 'students'])->name('students');
            Route::post('/students', [LessonsController::class, 'students_calculate'])->name('students_calculate');
        });

        Route::prefix('/time')->name('time.')->group(function () {
            Route::get('/period', [TimeController::class, 'period'])->name('period');
            Route::post('/period', [TimeController::class, 'period_calculate'])->name('period_calculate');
            Route::get('/students', [TimeController::class, 'students'])->name('students');
            Route::post('/students', [TimeController::class, 'students_calculate'])->name('students_calculate');
        });

    });
});
