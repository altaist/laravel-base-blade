<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::middleware('guest')->group(function () {
    Route::get('/register', function () { return view('auth.register'); })->name('register');
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');
});

require __DIR__.'/auth.php';
