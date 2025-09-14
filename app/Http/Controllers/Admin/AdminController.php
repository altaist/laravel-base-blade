<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use App\Services\PersonService;
use App\Models\User;
use App\Models\Feedback;
use App\Models\Article;
use App\Enums\ArticleStatus;
use App\Http\Requests\PersonEditRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct(
        private AdminUserService $adminUserService,
        private PersonService $personService
    ) {}

    /**
     * Главная страница админки
     */
    public function dashboard(): View
    {
        $userStats = $this->adminUserService->getUserStats();
        $feedbackStats = [
            'total' => Feedback::count(),
            'recent' => Feedback::where('created_at', '>=', now()->subDays(7))->count(),
        ];
        
        $articleStats = [
            'total' => Article::count(),
            'recent' => Article::where('created_at', '>=', now()->subDays(7))->count(),
            'published' => Article::where('status', ArticleStatus::PUBLISHED)->count(),
            'draft' => Article::where('status', ArticleStatus::DRAFT)->count(),
            'ready' => Article::where('status', ArticleStatus::READY_TO_PUBLISH)->count(),
        ];

        return view('pages.admin.dashboard', compact('userStats', 'feedbackStats', 'articleStats'));
    }

}
