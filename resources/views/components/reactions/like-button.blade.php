@props(['item', 'showCount' => true, 'class' => ''])

@php
    $isLiked = auth()->check() && $item->isLikedBy(auth()->user());
    $likesCount = $item->likes->count();
@endphp

<button class="reaction-btn like-btn {{ $isLiked ? 'active' : '' }} {{ $class }}" 
        data-type="like" 
        data-item-id="{{ $item->id }}"
        title="Лайк">
    <i class="fas fa-heart"></i>
    @if($showCount)
        <span class="reaction-count">{{ $likesCount }}</span>
    @endif
</button>

