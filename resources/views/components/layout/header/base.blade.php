@props([
    'variant' => 'public',
    'icon' => 'fas fa-cube',
    'title' => 'Kvadro',
    'titleLink' => '/',
    'mainMenu' => [],
    'userMenu' => [],
    'mobileMenu' => null
])

@php
    // Определяем пользовательское меню по умолчанию если не передано
    if (empty($userMenu)) {
        if (auth()->check()) {
            // Одинаковое меню для всех авторизованных пользователей
            $userMenu = [
                ['title' => 'Личный кабинет', 'route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ['title' => 'Профиль', 'route' => 'profile', 'icon' => 'fas fa-user'],
                ['title' => 'Админка', 'route' => 'admin.dashboard', 'icon' => 'fas fa-cog', 'class' => 'admin'],
                ['title' => 'Выйти', 'route' => 'logout', 'type' => 'form', 'icon' => 'fas fa-sign-out-alt']
            ];
        } else {
            // Меню для неавторизованных пользователей
            $userMenu = [
                ['title' => 'Вход', 'route' => 'login', 'icon' => 'fas fa-sign-in-alt']
            ];
        }
    }
@endphp

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
                            {{-- Выпадающее меню пользователя --}}
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-icon-square me-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header">
                                            <div class="user-name">{{ Auth::user()->name }}</div>
                                            <div class="user-role">
                                                @switch(Auth::user()->role->value)
                                                    @case('admin')
                                                        Администратор
                                                        @break
                                                    @case('manager')
                                                        Менеджер
                                                        @break
                                                    @case('user')
                                                        Пользователь
                                                        @break
                                                    @default
                                                        Пользователь
                                                @endswitch
                                            </div>
                                        </div>
                                    </li>
                                    @foreach($userMenu as $item)
                                        <li>
                                            @if(isset($item['type']) && $item['type'] === 'form')
                                                <form method="POST" action="{{ route($item['route']) }}" class="d-inline w-100">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        {{ $item['title'] }}
                                                    </button>
                                                </form>
                                            @else
                                                @if(isset($item['class']) && $item['class'] === 'admin')
                                                    @can('admin')
                                                        <a class="dropdown-item" href="{{ route($item['route']) }}">
                                                            {{ $item['title'] }}
                                                        </a>
                                                    @endcan
                                                @else
                                                    <a class="dropdown-item" href="{{ route($item['route']) }}">
                                                        {{ $item['title'] }}
                                                    </a>
                                                @endif
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
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
                            <div class="user-details">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">
                                    @switch(Auth::user()->role->value)
                                        @case('admin')
                                            Администратор
                                            @break
                                        @case('manager')
                                            Менеджер
                                            @break
                                        @case('user')
                                            Пользователь
                                            @break
                                        @default
                                            Пользователь
                                    @endswitch
                                </div>
                            </div>
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
                                    @if(isset($item['class']) && $item['class'] === 'admin')
                                        @can('admin')
                                            <a href="{{ route($item['route']) }}" class="menu-item {{ $item['class'] ?? '' }}">
                                                <i class="{{ $item['icon'] ?? 'fas fa-user' }}"></i>
                                                <span>{{ $item['title'] }}</span>
                                            </a>
                                        @endcan
                                    @else
                                        <a href="{{ route($item['route']) }}" class="menu-item {{ $item['class'] ?? '' }}">
                                            <i class="{{ $item['icon'] ?? 'fas fa-user' }}"></i>
                                            <span>{{ $item['title'] }}</span>
                                        </a>
                                    @endif
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
