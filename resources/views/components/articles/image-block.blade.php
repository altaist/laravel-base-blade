@props(['image', 'alt' => '', 'zoom' => false])

<div class="article-image-modern mb-5">
    <div class="image-container">
        <img src="{{ $image }}" alt="{{ $alt }}" class="article-image-main">
        @if($zoom)
            <div class="image-overlay">
                <div class="image-overlay-content">
                    <i class="fas fa-expand-arrows-alt"></i>
                </div>
            </div>
        @endif
    </div>
</div>
