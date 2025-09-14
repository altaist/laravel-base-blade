@props(['item', 'type' => 'article', 'variant' => 'default'])

@php
    // Определяем URL в зависимости от типа элемента
    $url = match($type) {
        'article' => route('article.show', $item->slug),
        default => '#'
    };
    
    // Определяем изображение
    $imageUrl = $item->imgFile?->url ?? asset('images/placeholder-card.svg');
    
    // Определяем заголовок
    $title = $item->name ?? $item->title ?? 'Без названия';
    
    // Определяем описание
    $description = $item->description ?? $item->content ?? '';
    $description = Str::limit($description, $variant === 'compact' ? 80 : 120);
@endphp

<div class="content-card content-card--{{ $type }} content-card--{{ $variant }}" data-item-id="{{ $item->id }}">
    <a href="{{ $url }}" class="content-card__link">
        <div class="content-card__image">
            <img src="{{ $imageUrl }}" alt="{{ $title }}" loading="lazy">
            <div class="content-card__overlay">
                <h3 class="content-card__title">{{ $title }}</h3>
            </div>
        </div>
        
        <div class="content-card__content">
            <p class="content-card__description">
                {{ $description }}
            </p>
        </div>
    </a>
    
    <div class="content-card__actions">
        <div class="content-card__reactions">
            <x-reactions.like-button :item="$item" />
            <x-reactions.favorite-button :item="$item" />
        </div>
        
        <a href="{{ $url }}" class="content-card__read-link">
            <span class="btn-text">Читать</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
