<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { 
    return view('home'); 
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/forgot-password', function () { 
        return view('auth.forgot-password'); 
    })->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () { 
        return view('profile'); 
    })->name('profile');
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});