@props(['article'])

<div class="article-list-item">
    <div class="row g-0">
        <div class="col-md-4">
            <div class="article-list-item__image-container">
                <img src="{{ $article->getImage() }}" 
                     alt="{{ $article->name }}" 
                     class="article-list-item__image">
            </div>
        </div>
        <div class="col-md-8">
            <div class="article-list-item__content">
                <h2 class="article-list-item__title">
                    <a href="{{ route('article.show', $article->slug) }}">
                        {{ $article->name }}
                    </a>
                </h2>
                
                <div class="article-list-item__meta">
                    <span class="article-list-item__meta-item">
                        <i class="fas fa-user"></i>
                        {{ $article->user->name ?? 'Автор' }}
                    </span>
                    <span class="article-list-item__meta-item">
                        <i class="fas fa-calendar"></i>
                        {{ $article->created_at->format('d.m.Y') }}
                    </span>
                </div>

                <p class="article-list-item__description">
                    {{ Str::limit($article->description ?? $article->content, 200) }}
                </p>

                <div class="article-list-item__footer">
                    <div class="article-list-item__stats">
                        <x-like-button :item="$article" :type="'article'" />
                        <x-favorite-button :item="$article" :type="'article'" />
                    </div>
                    <a href="{{ route('article.show', $article->slug) }}" 
                       class="article-list-item__read-link">
                        <span class="btn-text">Читать далее</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
