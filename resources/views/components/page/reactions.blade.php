@props(['item'])

<div class="article-reactions-modern">
    <x-reactions.like-button :item="$item" class="reaction-btn-modern" />
    <x-reactions.favorite-button :item="$item" class="reaction-btn-modern" />
</div>
