<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Логотип слева -->
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="fas fa-cube me-2"></i>
            Kvadro
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar" aria-controls="userNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="userNavbar">
            <!-- Центральное меню -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="/">
                        Главная
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">
                        О нас
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">
                        Услуги
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">
                        Контакты
                    </a>
                </li>
            </ul>
            
            <!-- Правая часть - пользовательские функции -->
            <ul class="navbar-nav">
                @guest
                    <!-- Desktop: Обычные ссылки -->
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link  btn btn-primary" href="{{ route('login') }}">
                            Вход
                        </a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link btn btn-primary" href="{{ route('register') }}">
                            Регистрация
                        </a>
                    </li>
                    
                    <!-- Mobile: Красивые кнопки по центру -->
                    <li class="nav-item d-lg-none">
                        <div class="mobile-guest-menu">
                            <div class="guest-actions">
                                <a class="btn btn-primary btn-lg w-100 mb-2" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Войти
                                </a>
                                <a class="btn btn-outline-primary btn-lg w-100" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Регистрация
                                </a>
                            </div>
                        </div>
                    </li>
                @else
                    @include('components.user-menu')
                @endguest
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar-nav .nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 600;
}

.navbar-nav .nav-link:hover {
    color: var(--bs-primary) !important;
}

.navbar-nav .nav-link.btn {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    text-decoration: none;
}

.navbar-nav .nav-link.btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white !important;
}

/* Отступ между кнопками в desktop версии */
@media (min-width: 992px) {
    .navbar-nav .nav-item:not(:last-child) {
        margin-right: 0.5rem;
    }
}

/* Мобильное меню для гостей */
.mobile-guest-menu {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 0.5rem 0;
    border: 1px solid #e9ecef;
}

.mobile-guest-menu .guest-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Адаптивность для мобильных устройств */
@media (max-width: 991.98px) {
    .navbar-nav.mx-auto {
        margin: 0 !important;
        text-align: center;
    }
    
    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
    }
    
    .dropdown-menu {
        width: 100%;
        text-align: center;
    }
    
    /* Выравнивание правого блока по центру на мобильных */
    .navbar-nav:last-child {
        justify-content: center;
        width: 100%;
        margin-top: 0.5rem;
    }
    
    .navbar-nav:last-child .nav-item {
        margin: 0 0.25rem;
    }
}
</style>
