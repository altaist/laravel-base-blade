<x-layout.header.base
    variant="public"
    icon="fas fa-cube"
    title="Kvadro"
    titleLink="{{ route('home') }}"
    :mainMenu="[
        ['title' => 'Главная', 'url' => route('home'), 'active' => 'home'],
        ['title' => 'Статьи', 'url' => route('articles.index'), 'active' => 'articles.*'],
        ['title' => 'Контакты', 'url' => '#contact']
    ]"
    :userMenu="[
        ['title' => 'Вход', 'route' => 'login', 'icon' => 'fas fa-sign-in-alt'],
        ['title' => 'Регистрация', 'route' => 'register', 'icon' => 'fas fa-user-plus']
    ]"
/>
