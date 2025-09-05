<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonEditController;
use App\Http\Controllers\AuthLinkController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TelegramAuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserFilesController;

Route::get('/', function () { 
    return view('home'); 
})->name('home');

// Feedback form
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Admin feedback routes
Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('admin.feedbacks.index');
    Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show'])->name('admin.feedbacks.show');
});

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

// Auth Link Routes - доступны без авторизации
Route::prefix('auth-link')->group(function () {
    Route::post('/generate', [AuthLinkController::class, 'generate'])->name('auth-link.generate');
    Route::post('/generate-registration', [AuthLinkController::class, 'generateRegistrationLink'])->name('auth-link.generate-registration');
    Route::get('/{token}', [AuthLinkController::class, 'authenticate'])->name('auth-link.authenticate');
});

// Auth Link Routes - требуют авторизации
Route::prefix('auth-link')->middleware('auth')->group(function () {
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
    
    // Редактирование персоны
    Route::get('/person/edit', [PersonEditController::class, 'edit'])->name('person.edit');
    Route::put('/person/update', [PersonEditController::class, 'update'])->name('person.update');
    Route::put('/person/update-address', [PersonEditController::class, 'updateAddress'])->name('person.update.address');
    Route::put('/person/update-additional-info', [PersonEditController::class, 'updateAdditionalInfo'])->name('person.update.additional-info');
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // User files routes
    Route::get('/files', [UserFilesController::class, 'index'])->name('user.files.index');
    Route::post('/files/upload', [UserFilesController::class, 'upload'])->name('user.files.upload');
    Route::get('/files/{file}/download', [UserFilesController::class, 'download'])->name('user.files.download');
    Route::post('/files/download-multiple', [UserFilesController::class, 'downloadMultiple'])->name('user.files.download-multiple');
    Route::delete('/files/{file}', [UserFilesController::class, 'delete'])->name('user.files.delete');
    Route::post('/files/{file}/toggle-public', [UserFilesController::class, 'togglePublic'])->name('user.files.toggle-public');
    
    // API File routes
    Route::post('/api/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::post('/api/files/upload-multiple', [FileController::class, 'uploadMultiple'])->name('files.upload-multiple');
    Route::get('/api/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/api/files/{file}/image', [FileController::class, 'showImage'])->name('files.image');
    Route::get('/img/{file}', [FileController::class, 'showImage'])->name('img.show');
    Route::delete('/api/files/{file}', [FileController::class, 'delete'])->name('files.delete');
    Route::post('/api/files/{file}/public-url', [FileController::class, 'createPublicUrl'])->name('files.public-url');
});

// Public file routes
Route::get('/files/public/{key}/download', [FileController::class, 'publicDownload'])->name('files.public.download');
Route::get('/files/public/{key}/image', [FileController::class, 'showPublicImage'])->name('files.public.image');
Route::get('/img/public/{key}', [FileController::class, 'showPublicImage'])->name('img.public');