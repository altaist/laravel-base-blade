<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthLinkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TelegramAuthController;

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

// Telegram Auth Routes
Route::get('/auth/telegram/callback', [TelegramAuthController::class, 'callback'])
    ->name('telegram.callback');

// Auth Link Routes
Route::prefix('auth-link')->group(function () {
    Route::post('/generate', [AuthLinkController::class, 'generate'])->name('auth-link.generate');
    Route::post('/generate-registration', [AuthLinkController::class, 'generateRegistrationLink'])->name('auth-link.generate-registration');
    Route::get('/{token}', [AuthLinkController::class, 'authenticate'])->name('auth-link.authenticate');
    Route::delete('/revoke', [AuthLinkController::class, 'revoke'])->name('auth-link.revoke');
    Route::get('/stats', [AuthLinkController::class, 'stats'])->name('auth-link.stats');
});

Route::middleware('auth')->group(function () {
    // Профиль пользователя
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/dashboard', [ProfileController::class, 'show'])->name('dashboard');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/address', [ProfileController::class, 'updateAddress'])->name('profile.update.address');
    Route::put('/profile/additional-info', [ProfileController::class, 'updateAdditionalInfo'])->name('profile.update.additional-info');
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});