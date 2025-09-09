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
            <ul class="navbar-nav mx-auto admin-nav">
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
                @include('components.user-menu')
            </ul>
        </div>
    </div>
</nav>

<style>
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
