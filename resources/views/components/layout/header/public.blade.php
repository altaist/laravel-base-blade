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
/>
