<?php

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::name('password.')->group(function () {
        Route::get('forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot.show');
        Route::post('forgot-password', [ForgotPasswordController::class, 'send_email'])->name('send-email');

        Route::get('reset-password/{token}', [ResetPasswordController::class, 'index'])->name('reset');
        Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('store');
    });

    Route::get('/registration', [RegistrationController::class, 'index'])->name('registration');
    Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');

    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'auth'])->name('login.auth');

});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

