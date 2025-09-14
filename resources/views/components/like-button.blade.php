@props([
    'item' => null,
    'type' => 'like',
    'showCount' => true,
    'size' => 'md',
    'class' => ''
])

@php
    // Определяем ID сущности из item
    if ($item) {
        $likeableId = $item->id;
        $isLiked = auth()->check() && $item->isLikedBy(auth()->user());
        $likesCount = $item->likes->count();
    } else {
        $likeableId = null;
        $isLiked = false;
        $likesCount = 0;
    }
@endphp

<div class="like-button-container {{ $class }}">
    @auth
        <button 
            type="button"
            class="reaction-btn like-btn {{ $isLiked ? 'active' : '' }} {{ $class }}"
            data-type="{{ $type }}"
            data-item-id="{{ $likeableId }}"
            data-item-type="{{ $type }}"
            title="Лайк"
        >
            <i class="fas fa-heart"></i>
            @if($showCount)
                <span class="reaction-count">{{ $likesCount }}</span>
            @endif
        </button>
    @else
        {{-- Для незарегистрированных пользователей показываем только счетчик --}}
        <div class="reaction-display {{ $class }}" title="Количество лайков">
            <i class="fas fa-heart text-muted"></i>
            @if($showCount)
                <span class="reaction-count text-muted">{{ $likesCount }}</span>
            @endif
        </div>
    @endauth
</div>
