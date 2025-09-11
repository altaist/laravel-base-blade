@props(['items'])

<nav aria-label="breadcrumb" class="mb-5">
    <ol class="breadcrumb-modern">
        @foreach($items as $item)
            <li class="breadcrumb-item-modern {{ !isset($item['url']) ? 'active' : '' }}">
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">
                        @if($loop->first)
                            <i class="fas fa-home"></i>
                        @endif
                        {{ $item['name'] }}
                    </a>
                @else
                    {{ $item['name'] }}
                @endif
            </li>
            @unless($loop->last)
                <li class="breadcrumb-separator">
                    <i class="fas fa-chevron-right"></i>
                </li>
            @endunless
        @endforeach
    </ol>
</nav>
