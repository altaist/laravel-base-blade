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
            
            <!-- Правая часть - пользовательские функции -->
            <ul class="navbar-nav">
                @include('components.user-menu')
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
