<x-layout.header.base
    variant="admin"
    icon="fas fa-cog"
    title="Админ панель"
    titleLink="{{ route('admin.dashboard') }}"
    :mainMenu="[
        ['title' => 'Панель управления', 'url' => route('admin.dashboard'), 'active' => 'admin.dashboard'],
        ['title' => 'Пользователи', 'url' => route('admin.users.index'), 'active' => 'admin.users.*'],
        ['title' => 'Обратная связь', 'url' => route('admin.feedbacks.index'), 'active' => 'admin.feedbacks.*'],
        ['title' => 'Статьи', 'url' => route('admin.articles.index'), 'active' => 'admin.articles.*']
    ]"
    :userMenu="[
        ['title' => 'Личный кабинет', 'route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['title' => 'Профиль', 'route' => 'profile', 'icon' => 'fas fa-user'],
        ['title' => 'Админка', 'route' => 'admin.dashboard', 'icon' => 'fas fa-cog', 'class' => 'admin'],
        ['title' => 'Выйти', 'route' => 'logout', 'type' => 'form', 'icon' => 'fas fa-sign-out-alt']
    ]"
/>
