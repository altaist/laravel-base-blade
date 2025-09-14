@props(['item', 'showCount' => true, 'class' => ''])

@php
    $isLiked = auth()->check() && $item->isLikedBy(auth()->user());
    $likesCount = $item->likes->count();
@endphp

@auth
    <button class="reaction-btn like-btn {{ $isLiked ? 'active' : '' }} {{ $class }}" 
            data-type="like" 
            data-item-id="{{ $item->id }}"
            title="Лайк">
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

