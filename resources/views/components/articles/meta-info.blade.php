@props(['items'])

<div class="article-meta-modern">
    @foreach($items as $item)
        <div class="article-meta-item">
            <div class="meta-icon">
                <i class="fas fa-{{ $item['icon'] }}"></i>
            </div>
            <span class="meta-text">{{ $item['text'] }}</span>
        </div>
    @endforeach
</div>
