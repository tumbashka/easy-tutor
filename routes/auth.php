<?php

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/registration', [RegistrationController::class, 'index'])->name('registration');
    Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');

    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'auth'])->name('login.auth');

});


Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

