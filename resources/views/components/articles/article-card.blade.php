@props(['article'])

@php
    // Используем простой SVG placeholder для карточек
    $imageUrl = $article->imgFile?->url ?? asset('images/placeholder-card.svg');
@endphp

<div class="article-card" data-article-id="{{ $article->id }}">
    <a href="{{ route('article.show', $article->slug) }}" class="article-card-link-wrapper">
        <div class="article-card-image">
            <img src="{{ $imageUrl }}" alt="{{ $article->name }}" loading="lazy">
            <div class="article-card-overlay">
                <h3 class="article-card-title">{{ $article->name }}</h3>
            </div>
        </div>
        
        <div class="article-card-content">
            <p class="article-card-description">
                {{ Str::limit($article->description ?? $article->content, 120) }}
            </p>
        </div>
    </a>
    
    <div class="article-card-actions">
        <div class="article-card-reactions">
            <x-like-button :item="$article" :type="'article'" />
            <x-favorite-button :item="$article" :type="'article'" />
        </div>
        
        <a href="{{ route('article.show', $article->slug) }}" class="article-card-read-link">
            <span class="btn-text">Читать</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
