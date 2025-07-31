<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

    Route::get('/home', function (){
        return 'student home page';
    })->name('home');

});
