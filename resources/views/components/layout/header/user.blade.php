<x-layout.header.base
    variant="user"
    icon="fas fa-user"
    title="Личный кабинет"
    titleLink="{{ route('home') }}"
    :mainMenu="[
        ['title' => 'Профиль', 'url' => route('dashboard'), 'active' => 'dashboard'],
        ['title' => 'Настройки', 'url' => route('profile'), 'active' => 'profile']
    ]"
/>
