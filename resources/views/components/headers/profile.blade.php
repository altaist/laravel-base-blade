<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Логотип слева -->
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="fas fa-cube me-2"></i>
            Kvadro
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cabinetNavbar" aria-controls="cabinetNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="cabinetNavbar">
            <!-- Центральное меню - пустое для личного кабинета -->
            <ul class="navbar-nav mx-auto">
                <!-- Здесь будет пустое место для будущего меню личного кабинета -->
            </ul>
            
            <!-- Правая часть - пользовательские функции (одинаковые везде) -->
            <ul class="navbar-nav">
                <!-- Desktop: Dropdown меню пользователя -->
                <li class="nav-item dropdown d-none d-lg-block">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-user-circle me-2"></i>
                                Личный кабинет
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.files.index') }}">
                                <i class="fas fa-folder me-2"></i>
                                Мои файлы
                            </a>
                        </li>
                        @can('admin')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cog me-2"></i>
                                    Админка
                                </a>
                            </li>
                        @endcan
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
                
                <!-- Mobile: Красивый блок меню пользователя -->
                <li class="nav-item d-lg-none">
                    <div class="mobile-user-menu">
                        <div class="user-info">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="fw-bold">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="user-actions">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard') }}">
                                Профиль
                            </a>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('user.files.index') }}">
                                Файлы
                            </a>
                            @can('admin')
                                <a class="btn btn-outline-warning btn-sm" href="{{ route('admin.dashboard') }}">
                                    Админка
                                </a>
                            @endcan
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Выход
                                </button>
                            </form>
                        </div>
                    </div>
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

/* Мобильный блок пользователя */
.mobile-user-menu {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 0.5rem 0;
    border: 1px solid #e9ecef;
}

.mobile-user-menu .user-info {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #dee2e6;
}

.mobile-user-menu .user-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

.mobile-user-menu .btn {
    flex: 1;
    min-width: 80px;
    font-size: 0.875rem;
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
