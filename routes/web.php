<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonEditController;
use App\Http\Controllers\AuthLinkController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Referral\ReferralController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TelegramAuthController;
use App\Http\Controllers\Files\FileController;
use App\Http\Controllers\Files\UserFilesController;
use App\Http\Controllers\Content\ArticleController;
use App\Http\Controllers\Content\StatusController;
use App\Http\Controllers\Public\HomeController;

// ===== PUBLIC ROUTES =====
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public article routes
Route::get('/article/{slug}', [ArticleController::class, 'showBySlug'])->name('article.show');

// ===== FEEDBACK ROUTES =====
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// ===== REFERRAL ROUTES =====
// Публичные маршруты для реферальных ссылок
Route::get('/ref/{code}', [ReferralController::class, 'handle'])->name('referral.handle');

// ===== ADMIN ROUTES =====
// Admin routes
Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Users management
    Route::resource('users', \App\Http\Controllers\Admin\Users\UserController::class)->names([
        'index' => 'admin.users.index',
        'show' => 'admin.users.show',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy'
    ]);
    
    // Feedbacks management
    Route::resource('feedbacks', \App\Http\Controllers\Admin\Feedbacks\FeedbackController::class)->names([
        'index' => 'admin.feedbacks.index',
        'show' => 'admin.feedbacks.show',
        'create' => 'admin.feedbacks.create',
        'store' => 'admin.feedbacks.store',
        'edit' => 'admin.feedbacks.edit',
        'update' => 'admin.feedbacks.update',
        'destroy' => 'admin.feedbacks.destroy'
    ]);
    
    // Articles management
    Route::resource('articles', \App\Http\Controllers\Admin\Articles\ArticleController::class)->names([
        'index' => 'admin.articles.index',
        'show' => 'admin.articles.show',
        'create' => 'admin.articles.create',
        'store' => 'admin.articles.store',
        'edit' => 'admin.articles.edit',
        'update' => 'admin.articles.update',
        'destroy' => 'admin.articles.destroy'
    ]);
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
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
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
    
    // ===== REFERRAL ROUTES =====
    Route::prefix('referral')->group(function () {
        Route::get('/', [ReferralController::class, 'list'])->name('referral.list');
        Route::post('/', [ReferralController::class, 'create'])->name('referral.create');
        Route::get('/stats', [ReferralController::class, 'stats'])->name('referral.stats');
        Route::get('/referred-users', [ReferralController::class, 'referredUsers'])->name('referral.referred-users');
        Route::put('/{link}', [ReferralController::class, 'update'])->name('referral.update');
        Route::post('/{link}/activate', [ReferralController::class, 'activate'])->name('referral.activate');
        Route::post('/{link}/deactivate', [ReferralController::class, 'deactivate'])->name('referral.deactivate');
    });
    
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
    
    // ===== ARTICLE ROUTES =====
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });
    
    // ===== STATUS ROUTES =====
    Route::prefix('status')->group(function () {
        Route::post('/{article}/change', [StatusController::class, 'changeStatus'])->name('status.change');
        Route::post('/{article}/publish', [StatusController::class, 'publish'])->name('status.publish');
        Route::post('/{article}/unpublish', [StatusController::class, 'unpublish'])->name('status.unpublish');
        Route::post('/{article}/ready', [StatusController::class, 'markAsReady'])->name('status.ready');
        Route::post('/{article}/draft', [StatusController::class, 'markAsDraft'])->name('status.draft');
    });
});

// ===== PUBLIC FILE ROUTES =====
Route::prefix('files/public')->group(function () {
    Route::get('/{key}/download', [FileController::class, 'publicDownload'])->name('files.public.download');
    Route::get('/{key}/image', [FileController::class, 'showPublicImage'])->name('files.public.image');
});

Route::get('/img/public/{key}', [FileController::class, 'showPublicImage'])->name('img.public');