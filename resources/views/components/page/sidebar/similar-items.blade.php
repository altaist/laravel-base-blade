@props(['items', 'title' => 'Похожие статьи'])

<div class="similar-articles-modern">
    <div class="sidebar-header">
        <h4 class="sidebar-title">{{ $title }}</h4>
        <div class="sidebar-decoration"></div>
    </div>
    
    <div class="similar-articles-list">
        @foreach($items as $item)
            <div class="similar-article-item-modern">
                <a href="{{ route($item->getRouteName(), $item->slug) }}" class="similar-article-link-modern">
                    <div class="similar-article-image-container">
                        <img src="{{ $item->getImage() }}" alt="{{ $item->name }}" class="similar-article-image-modern">
                    </div>
                    <div class="similar-article-content-modern">
                        <h5 class="similar-article-title">{{ $item->name }}</h5>
                        <p class="similar-article-description">{{ Str::limit($item->description ?? $item->content, 80) }}</p>
                        <div class="similar-article-meta">
                            <span class="similar-article-date">{{ $item->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
