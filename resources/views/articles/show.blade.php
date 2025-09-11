@extends('layouts.app')

@section('content')
<div class="article-page">
    <div class="container">
        {{-- Хлебные крошки --}}
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb-modern">
                <li class="breadcrumb-item-modern">
                    <a href="{{ route('home') }}" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        Главная
                    </a>
                </li>
                <li class="breadcrumb-separator">
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li class="breadcrumb-item-modern active">
                    {{ Str::limit($article->name, 50) }}
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                {{-- Заголовок статьи --}}
                <header class="article-header-modern mb-5">
                    <div class="article-header-content">
                        <h1 class="article-title-modern">{{ $article->name }}</h1>
                        
                        <div class="article-meta-modern">
                            <div class="article-meta-item">
                                <div class="meta-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="meta-text">{{ $article->user->name ?? 'Автор' }}</span>
                            </div>
                            
                            <div class="article-meta-item">
                                <div class="meta-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <span class="meta-text">{{ $article->created_at->format('d.m.Y') }}</span>
                            </div>
                            
                            <div class="article-meta-item">
                                <div class="meta-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <span class="meta-text">{{ $article->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="article-reactions-modern">
                        <button class="reaction-btn-modern like-btn {{ auth()->check() && $article->isLikedBy(auth()->user()) ? 'active' : '' }}" 
                                data-type="like" 
                                data-article-id="{{ $article->id }}"
                                title="Лайк">
                            <i class="fas fa-heart"></i>
                            <span class="reaction-count">{{ $article->likesCount() }}</span>
                        </button>
                        
                        <button class="reaction-btn-modern favorite-btn {{ auth()->check() && $article->isFavoritedBy(auth()->user()) ? 'active' : '' }}" 
                                data-type="favorite" 
                                data-article-id="{{ $article->id }}"
                                title="В избранное">
                            <i class="fas fa-star"></i>
                            <span class="reaction-count">{{ $article->favoritesCount() }}</span>
                        </button>
                    </div>
                </header>

                {{-- Изображение статьи --}}
                <div class="article-image-modern mb-5">
                    @php
                        $imageUrl = $article->imgFile?->url ?? asset('images/placeholder-large.svg');
                    @endphp
                    <div class="image-container">
                        <img src="{{ $imageUrl }}" alt="{{ $article->name }}" class="article-image-main">
                        <div class="image-overlay">
                            <div class="image-overlay-content">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Описание статьи --}}
                @if($article->description)
                    <div class="article-description-modern mb-5">
                        <div class="description-card">
                            <p class="description-text">{{ $article->description }}</p>
                        </div>
                    </div>
                @endif

                {{-- Содержимое статьи --}}
                <div class="article-content-modern">
                    <div class="content-card">
                        <div class="content-text">
                            {!! nl2br(e($article->content)) !!}
                        </div>
                    </div>
                </div>

                {{-- Дополнительные изображения (слайдер) --}}
                <div class="article-gallery-modern mt-5">
                    <div class="gallery-header">
                        <h3 class="gallery-title">Галерея</h3>
                        <div class="gallery-indicators">
                            <span class="indicator active" data-slide="0"></span>
                            <span class="indicator" data-slide="1"></span>
                            <span class="indicator" data-slide="2"></span>
                            <span class="indicator" data-slide="3"></span>
                            <span class="indicator" data-slide="4"></span>
                        </div>
                    </div>
                    
                    <div class="gallery-container">
                        <div id="articleGallery" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @for($i = 0; $i < 5; $i++)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <div class="gallery-image-container">
                                            <img src="{{ asset('images/placeholder-large.svg') }}" class="gallery-image" alt="Галерея {{ $i + 1 }}">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <button class="carousel-control-prev-modern" type="button" data-bs-target="#articleGallery" data-bs-slide="prev">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="carousel-control-next-modern" type="button" data-bs-target="#articleGallery" data-bs-slide="next">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Боковая панель --}}
            <div class="col-lg-4">
                <div class="sidebar-modern">
                    {{-- Похожие статьи --}}
                    <div class="similar-articles-modern">
                        <div class="sidebar-header">
                            <h4 class="sidebar-title">Похожие статьи</h4>
                            <div class="sidebar-decoration"></div>
                        </div>
                        
                        @php
                            $similarArticles = \App\Models\Article::where('status', \App\Enums\ArticleStatus::PUBLISHED)
                                ->where('id', '!=', $article->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(4)
                                ->get();
                        @endphp
                        
                        <div class="similar-articles-list">
                            @foreach($similarArticles as $similarArticle)
                                <div class="similar-article-item-modern">
                                    <a href="{{ route('article.show', $similarArticle->slug) }}" class="similar-article-link-modern">
                                        <div class="similar-article-image-container">
                                            <img src="{{ asset('images/placeholder-card.svg') }}" alt="{{ $similarArticle->name }}" class="similar-article-image-modern">
                                        </div>
                                        <div class="similar-article-content-modern">
                                            <h5 class="similar-article-title">{{ $similarArticle->name }}</h5>
                                            <p class="similar-article-description">{{ Str::limit($similarArticle->description ?? $similarArticle->content, 80) }}</p>
                                            <div class="similar-article-meta">
                                                <span class="similar-article-date">{{ $similarArticle->created_at->format('d.m.Y') }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Основные стили страницы */
.article-page {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

/* Хлебные крошки */
.breadcrumb-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    list-style: none;
    padding: 0;
    margin: 0;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.breadcrumb-item-modern {
    display: flex;
    align-items: center;
}

.breadcrumb-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

.breadcrumb-link:hover {
    color: #007bff;
    background: #f8f9fa;
    text-decoration: none;
}

.breadcrumb-separator {
    color: #dee2e6;
    font-size: 0.8rem;
}

.breadcrumb-item-modern.active {
    color: #495057;
    font-weight: 600;
    padding: 0.5rem 1rem;
    background: #e9ecef;
    border-radius: 10px;
}

/* Заголовок статьи */
.article-header-modern {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.article-title-modern {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.article-meta-modern {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.article-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.meta-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.meta-text {
    font-weight: 500;
}

.article-reactions-modern {
    display: flex;
    gap: 1rem;
    flex-shrink: 0;
}

.reaction-btn-modern {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    padding: 0.75rem 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 600;
}

.reaction-btn-modern:hover {
    border-color: #007bff;
    color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}

.reaction-btn-modern.active {
    border-color: #007bff;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.reaction-btn-modern.active.like-btn {
    border-color: #dc3545;
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.reaction-btn-modern.active.favorite-btn {
    border-color: #ffc107;
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #212529;
}

/* Изображение статьи */
.article-image-modern {
    position: relative;
}

.image-container {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.article-image-main {
    width: 100%;
    height: auto;
    display: block;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.image-container:hover .image-overlay {
    opacity: 1;
}

.image-overlay-content {
    color: white;
    font-size: 2rem;
}

/* Описание статьи */
.article-description-modern {
    margin-bottom: 2rem;
}

.description-card {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #007bff;
}

.description-text {
    font-size: 1.2rem;
    color: #495057;
    line-height: 1.6;
    margin: 0;
    font-style: italic;
}

/* Содержимое статьи */
.article-content-modern {
    margin-bottom: 3rem;
}

.content-card {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.content-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #2c3e50;
}

.content-text p {
    margin-bottom: 1.5rem;
}

/* Галерея */
.article-gallery-modern {
    margin-top: 3rem;
}

.gallery-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.gallery-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.gallery-indicators {
    display: flex;
    gap: 0.5rem;
}

.indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #e9ecef;
    cursor: pointer;
    transition: all 0.3s ease;
}

.indicator.active {
    background: #007bff;
    transform: scale(1.2);
}

.gallery-container {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    position: relative;
}

.gallery-image-container {
    border-radius: 15px;
    overflow: hidden;
}

.gallery-image {
    width: 100%;
    height: auto;
    display: block;
}

.carousel-control-prev-modern,
.carousel-control-next-modern {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    border: none;
    color: white;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-control-prev-modern {
    left: 20px;
}

.carousel-control-next-modern {
    right: 20px;
}

.carousel-control-prev-modern:hover,
.carousel-control-next-modern:hover {
    background: rgba(0, 0, 0, 0.7);
    transform: translateY(-50%) scale(1.1);
    color: white;
}


/* Боковая панель */
.sidebar-modern {
    position: sticky;
    top: 2rem;
}

.sidebar-header {
    margin-bottom: 2rem;
}

.sidebar-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.sidebar-decoration {
    width: 50px;
    height: 4px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 2px;
}

/* Похожие статьи */
.similar-articles-modern {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.similar-articles-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.similar-article-item-modern {
    background: #f8f9fa;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.similar-article-item-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: #007bff;
}

.similar-article-link-modern {
    display: block;
    text-decoration: none;
    color: inherit;
}

.similar-article-link-modern:hover {
    text-decoration: none;
    color: inherit;
}

.similar-article-image-container {
    height: 120px;
    overflow: hidden;
}

.similar-article-image-modern {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.similar-article-item-modern:hover .similar-article-image-modern {
    transform: scale(1.05);
}

.similar-article-content-modern {
    padding: 1.5rem;
}

.similar-article-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #2c3e50;
    line-height: 1.3;
}

.similar-article-description {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.similar-article-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.similar-article-date {
    font-size: 0.8rem;
    color: #adb5bd;
    font-weight: 500;
}

/* Адаптивность */
@media (max-width: 768px) {
    .article-header-modern {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .article-title-modern {
        font-size: 2rem;
    }
    
    .article-meta-modern {
        gap: 1rem;
    }
    
    .article-reactions-modern {
        align-self: stretch;
        justify-content: center;
    }
    
    .gallery-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .breadcrumb-modern {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/articles.js') }}"></script>
@endpush
