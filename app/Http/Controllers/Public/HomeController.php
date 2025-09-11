<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Enums\ArticleStatus;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct() {}

    /**
     * Показать главную страницу с последними статьями
     */
    public function index(): View
    {
        // Получаем последние 6 опубликованных статей
        $articles = Article::where('status', ArticleStatus::PUBLISHED)
            ->with(['user', 'imgFile'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Определяем, какую страницу показывать
        $view = request()->is('home2') ? 'pages.home2' : 'pages.home';

        return view($view, compact('articles'));
    }
}
