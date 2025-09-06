<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonEditController;
use App\Http\Controllers\AuthLinkController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TelegramAuthController;
use App\Http\Controllers\Files\FileController;
use App\Http\Controllers\Files\UserFilesController;

// ===== PUBLIC ROUTES =====
Route::get('/', function () { 
    return view('home'); 
})->name('home');

// ===== FEEDBACK ROUTES =====
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// ===== ADMIN ROUTES =====
// Admin routes
Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Users management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/{user}/edit', [AdminController::class, 'userEdit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');
    
    // Feedback routes
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('admin.feedbacks.index');
    Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show'])->name('admin.feedbacks.show');
});

// ===== GUEST AUTH ROUTES =====
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/forgot-password', function () { 
        return view('auth.forgot-password'); 
    })->name('password.request');
});

// ===== TELEGRAM AUTH ROUTES =====
Route::get('/auth/telegram/callback', [TelegramAuthController::class, 'callback'])
    ->name('telegram.callback');

// ===== AUTH LINK ROUTES =====
Route::prefix('auth-link')->group(function () {
    // Public auth link routes
    Route::post('/generate', [AuthLinkController::class, 'generate'])->name('auth-link.generate');
    Route::post('/generate-registration', [AuthLinkController::class, 'generateRegistrationLink'])->name('auth-link.generate-registration');
    Route::get('/{token}', [AuthLinkController::class, 'authenticate'])->name('auth-link.authenticate');
    
    // Protected auth link routes
    Route::middleware('auth')->group(function () {
        Route::delete('/revoke', [AuthLinkController::class, 'revoke'])->name('auth-link.revoke');
        Route::get('/stats', [AuthLinkController::class, 'stats'])->name('auth-link.stats');
    });
});

Route::middleware('auth')->group(function () {
    // ===== USER PROFILE ROUTES =====
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/dashboard', [ProfileController::class, 'show'])->name('dashboard');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/address', [ProfileController::class, 'updateAddress'])->name('profile.update.address');
    Route::put('/profile/additional-info', [ProfileController::class, 'updateAdditionalInfo'])->name('profile.update.additional-info');
    
    // ===== PERSON MANAGEMENT ROUTES =====
    Route::prefix('person')->group(function () {
        Route::get('/edit', [PersonEditController::class, 'edit'])->name('person.edit');
        Route::put('/update', [PersonEditController::class, 'update'])->name('person.update');
        Route::put('/update-address', [PersonEditController::class, 'updateAddress'])->name('person.update.address');
        Route::put('/update-additional-info', [PersonEditController::class, 'updateAdditionalInfo'])->name('person.update.additional-info');
    });
    
    // ===== AUTH ROUTES =====
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // ===== FILE ROUTES =====
    
    // User Files Management
    Route::prefix('files')->group(function () {
        // Main file operations
        Route::get('/', [UserFilesController::class, 'index'])->name('user.files.index');
        Route::post('/upload', [UserFilesController::class, 'upload'])->name('user.files.upload');
        Route::post('/download-multiple', [UserFilesController::class, 'downloadMultiple'])->name('user.files.download-multiple');
        
        // File categories
        Route::get('/images', [UserFilesController::class, 'getImages'])->name('user.files.images');
        Route::get('/documents', [UserFilesController::class, 'getDocuments'])->name('user.files.documents');
        Route::get('/stats', [UserFilesController::class, 'getStats'])->name('user.files.stats');
        
        // Individual file operations
        Route::get('/{file}/download', [UserFilesController::class, 'download'])->name('user.files.download');
        Route::delete('/{file}', [UserFilesController::class, 'delete'])->name('user.files.delete');
        Route::post('/{file}/toggle-public', [UserFilesController::class, 'togglePublic'])->name('user.files.toggle-public');
    });
    
    // API File Routes
    Route::prefix('api/files')->group(function () {
        Route::post('/upload', [FileController::class, 'upload'])->name('files.upload');
        Route::post('/upload-multiple', [FileController::class, 'uploadMultiple'])->name('files.upload-multiple');
        Route::get('/{file}/download', [FileController::class, 'download'])->name('files.download');
        Route::get('/{file}/image', [FileController::class, 'showImage'])->name('files.image');
        Route::delete('/{file}', [FileController::class, 'delete'])->name('files.delete');
        Route::post('/{file}/public-url', [FileController::class, 'createPublicUrl'])->name('files.public-url');
    });
    
    // Image display routes
    Route::get('/img/{file}', [FileController::class, 'showImage'])->name('img.show');
});

// ===== PUBLIC FILE ROUTES =====
Route::prefix('files/public')->group(function () {
    Route::get('/{key}/download', [FileController::class, 'publicDownload'])->name('files.public.download');
    Route::get('/{key}/image', [FileController::class, 'showPublicImage'])->name('files.public.image');
});

Route::get('/img/public/{key}', [FileController::class, 'showPublicImage'])->name('img.public');