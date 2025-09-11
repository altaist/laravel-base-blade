@props(['item'])

<div class="article-reactions-modern">
    <button class="reaction-btn-modern like-btn {{ auth()->check() && $item->isLikedBy(auth()->user()) ? 'active' : '' }}" 
            data-type="like" 
            data-id="{{ $item->id }}"
            title="Лайк">
        <i class="fas fa-heart"></i>
        <span class="reaction-count">{{ $item->likesCount() }}</span>
    </button>
    
    <button class="reaction-btn-modern favorite-btn {{ auth()->check() && $item->isFavoritedBy(auth()->user()) ? 'active' : '' }}" 
            data-type="favorite" 
            data-id="{{ $item->id }}"
            title="В избранное">
        <i class="fas fa-star"></i>
        <span class="reaction-count">{{ $item->favoritesCount() }}</span>
    </button>
</div>
