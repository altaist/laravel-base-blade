@props(['title', 'subtitle' => null, 'meta' => null])

<header class="page-header">
    <div class="page-header__content">
        <h1 class="page-header__title">{{ $title }}</h1>
        
        @if($subtitle)
            <p class="page-header__subtitle">{{ $subtitle }}</p>
        @endif
        
        @if($meta)
            <div class="page-header__meta">
                {{ $meta }}
            </div>
        @endif
        
        {{ $slot }}
    </div>
    
    @if(isset($actions))
        <div class="page-header__actions">
            {{ $actions }}
        </div>
    @endif
</header>
