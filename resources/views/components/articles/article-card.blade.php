@props(['article'])

@php
    // Используем разные сервисы для моковых изображений
    $imageServices = [
        'https://picsum.photos/400/250?random=' . $article->id,
        'https://via.placeholder.com/400x250/007bff/ffffff?text=Quad+Bike',
        'https://source.unsplash.com/400x250/?quadbike,atv,offroad',
        'https://images.pexels.com/photos/1558618666/pexels-photo-1558618666.jpeg?auto=compress&cs=tinysrgb&w=400&h=250&fit=crop',
        'https://loremflickr.com/400/250/quadbike,atv'
    ];
    $imageUrl = $article->imgFile?->url ?? $imageServices[$article->id % count($imageServices)];
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
