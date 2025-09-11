@props(['title'])

<header class="article-header-modern mb-5">
    <div class="article-header-content">
        <h1 class="article-title-modern">{{ $title }}</h1>
        {{ $slot }}
    </div>
    
    @if(isset($actions))
        <div class="article-reactions-modern">
            {{ $actions }}
        </div>
    @endif
</header>
