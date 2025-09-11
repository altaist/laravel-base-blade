@extends('layouts.page')

@section('page-content')
    @php
        $breadcrumbs = [
            ['name' => 'Главная', 'url' => route('home')],
            ['name' => 'Статьи']
        ];
    @endphp

    <x-page.breadcrumbs :items="$breadcrumbs" />

    <div class="row">
        <div class="col-lg-8">
            <x-page.header title="Статьи">
                <x-page.meta-info :items="[
                    ['icon' => 'newspaper', 'text' => 'Всего статей: ' . $articles->total()],
                ]" />
            </x-page.header>

            <div class="articles-list">
                @foreach($articles as $article)
                    <div class="article-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="article-image-container">
                                    <img src="{{ $article->getImage() }}" 
                                         alt="{{ $article->name }}" 
                                         class="article-image">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="article-content">
                                    <h2 class="article-title">
                                        <a href="{{ route('article.show', $article->slug) }}">
                                            {{ $article->name }}
                                        </a>
                                    </h2>
                                    
                                    <div class="article-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-user"></i>
                                            {{ $article->user->name ?? 'Автор' }}
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            {{ $article->created_at->format('d.m.Y') }}
                                        </span>
                                    </div>

                                    <p class="article-description">
                                        {{ Str::limit($article->description ?? $article->content, 200) }}
                                    </p>

                                    <div class="article-footer">
                                        <div class="article-stats">
                                            <x-reactions.like-button :item="$article" />
                                            <x-reactions.favorite-button :item="$article" />
                                        </div>
                                        <a href="{{ route('article.show', $article->slug) }}" 
                                           class="read-more-btn">
                                            <span class="btn-text">Читать далее</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-container">
                <x-page.pagination :paginator="$articles" />
            </div>
        </div>

        <div class="col-lg-4">
            <x-page.sidebar.wrapper>
                @if($popularArticles->isNotEmpty())
                    <x-page.sidebar.similar-items 
                        :items="$popularArticles"
                        title="Популярные статьи"
                    />
                @endif
            </x-page.sidebar.wrapper>
        </div>
    </div>
@endsection

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/components/page.css') }}">
@endpush

