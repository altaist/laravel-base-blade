@props(['article'])

@php
    // Используем простой SVG placeholder для карточек
    $imageUrl = $article->imgFile?->url ?? asset('images/placeholder-card.svg');
    $isLiked = auth()->check() && $article->isLikedBy(auth()->user());
    $isFavorited = auth()->check() && $article->isFavoritedBy(auth()->user());
    $likesCount = $article->likesCount();
    $favoritesCount = $article->favoritesCount();
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
            <button class="reaction-btn like-btn {{ $isLiked ? 'active' : '' }}" 
                    data-type="like" 
                    data-article-id="{{ $article->id }}"
                    title="Лайк">
                <i class="fas fa-heart"></i>
                <span class="reaction-count">{{ $likesCount }}</span>
            </button>
            
            <button class="reaction-btn favorite-btn {{ $isFavorited ? 'active' : '' }}" 
                    data-type="favorite" 
                    data-article-id="{{ $article->id }}"
                    title="В избранное">
                <i class="fas fa-star"></i>
                <span class="reaction-count">{{ $favoritesCount }}</span>
            </button>
        </div>
        
        <a href="{{ route('article.show', $article->slug) }}" class="article-card-read-link">
            Читать
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
