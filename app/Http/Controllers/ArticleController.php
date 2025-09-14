<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Enums\ArticleStatus;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::where('status', ArticleStatus::PUBLISHED)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $popularArticles = Article::where('status', ArticleStatus::PUBLISHED)
            ->orderBy('likes_count', 'desc')
            ->limit(4)
            ->get();

        return view('pages.public.articles.index', compact('articles', 'popularArticles'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('status', ArticleStatus::PUBLISHED)
            ->firstOrFail();

        return view('pages.public.articles.show', compact('article'));
    }
}
