{{-- Header2 в стиле хлебных крошек --}}
<header class="header2">
    <div class="container-fluid">
        <div class="container">
            <div class="header2-content">
                <div class="d-flex justify-content-between align-items-center">
            {{-- Логотип --}}
            <div class="header2-brand">
                <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center">
                    <i class="fas fa-cube me-2 text-primary"></i>
                    <h1 class="header2-title">Kvadro</h1>
                </a>
            </div>
            

            
            {{-- Кнопки действий --}}
            <div class="header2-actions d-none d-lg-block">
                @guest
                    <!-- Desktop: Только кнопка входа -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('login') }}" class="header2-nav-link active">
                            Вход
                        </a>
                    </div>
                @else
                    <!-- Desktop: Стильные кнопки без иконок -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="header2-nav-link">
                            Профиль
                        </a>
                        <a href="{{ route('profile') }}" class="header2-nav-link">
                            Настройки
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="header2-nav-link">
                            Админка
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="header2-nav-link logout-btn">
                                Выйти
                            </button>
                        </form>
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
    
    {{-- Мобильное меню --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Меню</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="header2-mobile-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Главная</a>
                    </li>
                </ul>
                
                <hr class="my-3">
                
                @auth
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Профиль</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg w-100">Выйти</button>
                        </form>
                    </div>
                @else
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Войти</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Регистрация</a>
                    </div>
                @endauth
            </nav>
        </div>
    </div>
</header>

<style>
/* Стили для header2 в стиле хлебных крошек */
.header2 {
    position: sticky;
    top: 0;
    z-index: 1030;
    padding-top: 0;
}

.header2-content {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

/* Состояние при скроллинге */
.header2.scrolled {
    background: white;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.header2.scrolled .header2-content {
    background: transparent;
    border-radius: 0;
    box-shadow: none;
}

.header2-brand .header2-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    transition: color 0.2s ease;
}

.header2-brand .header2-title:hover {
    color: #0d6efd;
}

.header2-nav .nav-link {
    color: #6c757d;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
}

.header2-nav .nav-link:hover {
    color: #0d6efd;
}

/* Кнопки в стиле хлебных крошек */
.header2-nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

.header2-nav-link:hover {
    color: #007bff;
    background: #f8f9fa;
    text-decoration: none;
}

.header2-nav-link.active {
    color: #495057;
    font-weight: 600;
    background: #e9ecef;
}

.header2-nav-link.logout-btn {
    background: none;
    border: none;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    cursor: pointer;
}

.header2-nav-link.logout-btn:hover {
    color: #dc3545;
    background: #f8f9fa;
}

.header2-actions .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.header2-mobile .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.header2-mobile-nav .nav-link {
    color: #6c757d;
    padding: 0.75rem 0;
    font-weight: 500;
    transition: color 0.2s ease;
}

.header2-mobile-nav .nav-link:hover {
    color: #0d6efd;
}

/* Адаптивность */
@media (max-width: 768px) {
    .header2-brand .header2-title {
        font-size: 1.25rem;
    }
    
    .header2-actions .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Мобильная версия - всегда как при скроллинге */
    .header2 {
        padding-top: 0 !important;
        background: white !important;
        box-shadow: none !important;
    }
    
    .header2-content {
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 0.75rem 0.0625rem !important;
    }
}
</style>

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
