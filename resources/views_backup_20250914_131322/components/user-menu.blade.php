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
                <i class="fas fa-tachometer-alt me-2"></i>
                Личный кабинет
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('profile') }}">
                <i class="fas fa-user me-2"></i>
                Профиль
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

<!-- Mobile: Улучшенный блок меню пользователя -->
<li class="nav-item d-lg-none">
    <div class="mobile-user-menu">
        <div class="user-info">
            <span class="fw-bold text-primary">{{ Auth::user()->name }}</span>
        </div>
        <div class="user-actions">
            <a class="btn btn-primary btn-sm w-100 mb-2" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>
                Личный кабинет
            </a>
            <a class="btn btn-primary btn-sm w-100 mb-2" href="{{ route('profile') }}">
                <i class="fas fa-user me-2"></i>
                Профиль
            </a>
            @can('admin')
                <a class="btn btn-warning btn-sm w-100 mb-2" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-cog me-2"></i>
                    Админка
                </a>
            @endcan
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Выход
                </button>
            </form>
        </div>
    </div>
</li>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: bold;
}

/* Мобильный блок пользователя */
.mobile-user-menu {
    background: #f8f9fa;
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin: 0.75rem 0;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-user-menu .user-info {
    text-align: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.mobile-user-menu .user-info span {
    font-size: 1.1rem;
}

.mobile-user-menu .user-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.mobile-user-menu .btn {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.mobile-user-menu .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
</style>
