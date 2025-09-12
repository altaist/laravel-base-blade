@props(['item', 'showCount' => true, 'class' => ''])

@php
    $isFavorited = auth()->check() && $item->isFavoritedBy(auth()->user());
    $favoritesCount = $item->favorites->count();
@endphp

@auth
    <button class="reaction-btn favorite-btn {{ $isFavorited ? 'active' : '' }} {{ $class }}" 
            data-type="favorite" 
            data-item-id="{{ $item->id }}"
            title="В избранное">
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

