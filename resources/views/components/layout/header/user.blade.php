<x-layout.header.base
    variant="user"
    icon="fas fa-user"
    title="Личный кабинет"
    titleLink="{{ route('dashboard') }}"
    :mainMenu="[
        ['title' => 'Профиль', 'url' => route('dashboard'), 'active' => 'dashboard'],
        ['title' => 'Настройки', 'url' => route('profile'), 'active' => 'profile']
    ]"
/>
