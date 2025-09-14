@props([
    'variant' => 'public',
    'icon' => 'fas fa-cube',
    'title' => 'Kvadro',
    'titleLink' => '/',
    'mainMenu' => [],
    'userMenu' => [],
    'mobileMenu' => null
])

<header class="header2">
    <div class="container-fluid">
        <div class="container">
            <div class="header2-content">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- Логотип --}}
                    <div class="header2-brand">
                        <a href="{{ $titleLink }}" class="text-decoration-none d-flex align-items-center">
                            <i class="{{ $icon }} me-2 text-primary"></i>
                            <h1 class="header2-title">{{ $title }}</h1>
                        </a>
                    </div>
                    
                    {{-- Навигация --}}
                    <nav class="header2-nav d-none d-md-flex">
                        <div class="d-flex align-items-center gap-2">
                            @foreach($mainMenu as $item)
                                <a href="{{ $item['url'] ?? route($item['route']) }}" class="header2-nav-link {{ request()->routeIs($item['active'] ?? $item['route'] ?? '') ? 'active' : '' }}">
                                    {{ $item['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </nav>
                    
                    {{-- Кнопки действий --}}
                    <div class="header2-actions d-none d-lg-block">
                        @guest
                            @foreach($userMenu as $item)
                                <a href="{{ $item['route'] }}" class="header2-nav-link {{ $item['active'] ?? '' }}">
                                    {{ $item['title'] }}
                                </a>
                            @endforeach
                        @else
                            @foreach($userMenu as $item)
                                @if(isset($item['type']) && $item['type'] === 'form')
                                    <form method="POST" action="{{ route($item['route']) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="header2-nav-link logout-btn">
                                            {{ $item['title'] }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route($item['route']) }}" class="header2-nav-link">
                                        {{ $item['title'] }}
                                    </a>
                                @endif
                            @endforeach
                        @endguest
                    </div>
                    
                    {{-- Мобильное меню --}}
                    <div class="header2-mobile d-md-none">
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Мобильное меню --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Меню</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="header2-mobile-nav">
                <ul class="nav flex-column">
                    @foreach($mainMenu as $item)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $item['url'] ?? route($item['route']) }}">{{ $item['title'] }}</a>
                        </li>
                    @endforeach
                </ul>
                
                <hr class="my-3">
                
                @auth
                    <div class="mobile-menu-section">
                        <div class="user-info mb-3">
                            <div class="avatar-circle">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="fw-bold">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="menu-actions">
                            @foreach($userMenu as $item)
                                @if(isset($item['type']) && $item['type'] === 'form')
                                    <form method="POST" action="{{ route($item['route']) }}">
                                        @csrf
                                        <button type="submit" class="menu-item logout">
                                            <i class="{{ $item['icon'] ?? 'fas fa-sign-out-alt' }}"></i>
                                            <span>{{ $item['title'] }}</span>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route($item['route']) }}" class="menu-item {{ $item['class'] ?? '' }}">
                                        <i class="{{ $item['icon'] ?? 'fas fa-user' }}"></i>
                                        <span>{{ $item['title'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="menu-actions">
                        @foreach($userMenu as $item)
                            <a href="{{ route($item['route']) }}" class="menu-item">
                                <i class="{{ $item['icon'] ?? 'fas fa-sign-in-alt' }}"></i>
                                <span>{{ $item['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endauth
            </nav>
        </div>
    </div>
</header>

<script>
// Адаптивный header при скроллинге
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.header2');
    
    function handleScroll() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
    
    window.addEventListener('scroll', handleScroll);
    
    // Проверяем начальное состояние
    handleScroll();
});
</script>
