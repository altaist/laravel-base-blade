@props(['items'])

<nav aria-label="breadcrumb" class="breadcrumbs">
    <ol class="breadcrumbs__list">
        @foreach($items as $item)
            <li class="breadcrumbs__item {{ !isset($item['url']) ? 'breadcrumbs__item--active' : '' }}">
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="breadcrumbs__link">
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
                <li class="breadcrumbs__separator">
                    <i class="fas fa-chevron-right"></i>
                </li>
            @endunless
        @endforeach
    </ol>
</nav>
