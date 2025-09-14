<x-layout.header.base
    variant="user"
    icon="fas fa-user"
    title="Личный кабинет"
    titleLink="{{ route('dashboard') }}"
    :mainMenu="[
        ['title' => 'Профиль', 'url' => route('dashboard'), 'active' => 'dashboard'],
        ['title' => 'Настройки', 'url' => route('profile'), 'active' => 'profile']
    ]"
    :userMenu="[
        ['title' => 'Личный кабинет', 'route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['title' => 'Профиль', 'route' => 'profile', 'icon' => 'fas fa-user'],
        ['title' => 'Админка', 'route' => 'admin.dashboard', 'icon' => 'fas fa-cog', 'class' => 'admin'],
        ['title' => 'Выйти', 'route' => 'logout', 'type' => 'form', 'icon' => 'fas fa-sign-out-alt']
    ]"
/>
