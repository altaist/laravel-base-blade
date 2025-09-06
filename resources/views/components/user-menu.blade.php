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
                Личный кабинет
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
</style>
