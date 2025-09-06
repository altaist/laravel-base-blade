<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Логотип и название -->
        <a class="navbar-brand fw-bold text-primary" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-cog me-2"></i>
            Админ панель
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbar">
            <!-- Основное меню админки по центру -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        Панель управления
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        Пользователи
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}" 
                       href="{{ route('admin.feedbacks.index') }}">
                        Обратная связь
                    </a>
                </li>
            </ul>
            
            <!-- Правая часть навигации -->
            <ul class="navbar-nav">
                <!-- Информация о текущем пользователе -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user-shield me-1"></i>
                                Администратор
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-user me-2"></i>
                                Личный кабинет
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}">
                                <i class="fas fa-home me-2"></i>
                                Главная страница
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Выход
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: bold;
}

.navbar-nav .nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 600;
}

.navbar-nav .nav-link:hover {
    color: var(--bs-primary) !important;
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
