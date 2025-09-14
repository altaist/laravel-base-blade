@props([
    'item' => null,
    'type' => 'favorite',
    'showCount' => true,
    'size' => 'md',
    'class' => ''
])

@php
    // Определяем ID сущности из item
    if ($item) {
        $favoritableId = $item->id;
        $isFavorited = auth()->check() && $item->isFavoritedBy(auth()->user());
        $favoritesCount = $item->favorites->count();
    } else {
        $favoritableId = null;
        $isFavorited = false;
        $favoritesCount = 0;
    }
@endphp

<div class="favorite-button-container {{ $class }}">
    @auth
        <button 
            type="button"
            class="reaction-btn favorite-btn {{ $isFavorited ? 'active' : '' }} {{ $class }}"
            data-type="{{ $type }}"
            data-item-id="{{ $favoritableId }}"
            data-item-type="{{ $type }}"
            title="В избранное"
        >
            <i class="fas fa-star"></i>
            @if($showCount)
                <span class="reaction-count">{{ $favoritesCount }}</span>
            @endif
        </button>
    @else
        {{-- Для незарегистрированных пользователей показываем только счетчик --}}
        <div class="reaction-display {{ $class }}" title="Количество добавлений в избранное">
            <i class="fas fa-star text-muted"></i>
            @if($showCount)
                <span class="reaction-count text-muted">{{ $favoritesCount }}</span>
            @endif
        </div>
    @endauth
</div>
