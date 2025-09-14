@extends('layouts.public')

@section('page-content')
    {{-- Hero секция --}}
    @include('components.landing.hero', [
        'title' => 'Добро пожаловать в Kvadro!',
        'subtitle' => 'Лучшие квадроциклы и аксессуары для ваших приключений. Качество, надежность и страсть к движению.',
        'buttonText' => 'Смотреть каталог',
        'buttonUrl' => '#catalog',
        'backgroundImage' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&h=600&fit=crop'
    ])

    {{-- Преимущества --}}
    @include('components.landing.features', [
        'title' => 'Почему выбирают нас',
        'subtitle' => 'Мы предлагаем только лучшее для ваших приключений',
        'features' => [
            [
                'icon' => 'fa-shipping-fast',
                'title' => 'Быстрая доставка',
                'description' => 'Доставляем квадроциклы по всей России в течение 3-7 дней'
            ],
            [
                'icon' => 'fa-shield-alt',
                'title' => 'Гарантия качества',
                'description' => 'Официальная гарантия на все модели от производителя'
            ],
            [
                'icon' => 'fa-headset',
                'title' => 'Поддержка 24/7',
                'description' => 'Круглосуточная техническая поддержка и консультации'
            ]
        ]
    ])

    {{-- Статьи --}}
    <x-articles.articles-section :articles="$articles" />

    {{-- Галерея --}}
    @include('components.landing.gallery', [
        'title' => 'Наши квадроциклы',
        'images' => [
            [
                [
                    'url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop',
                    'alt' => 'Спортивный квадроцикл',
                    'title' => 'Спортивная модель',
                    'description' => 'Высокая скорость и маневренность'
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
                    'alt' => 'Утилитарный квадроцикл',
                    'title' => 'Утилитарная модель',
                    'description' => 'Надежность для работы и отдыха'
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=400&h=300&fit=crop',
                    'alt' => 'Детский квадроцикл',
                    'title' => 'Детская модель',
                    'description' => 'Безопасность для маленьких водителей'
                ]
            ]
        ]
    ])

    {{-- Тарифы --}}
    @include('components.landing.pricing', [
        'title' => 'Наши услуги',
        'subtitle' => 'Выберите подходящий пакет услуг',
        'plans' => [
            [
                'name' => 'Базовый',
                'price' => '₽15,000',
                'period' => 'месяц',
                'description' => 'Для начинающих райдеров',
                'features' => [
                    'Аренда квадроцикла на 1 день',
                    'Базовая экипировка',
                    'Инструктаж по безопасности',
                    'Страховка'
                ],
                'button' => [
                    'text' => 'Заказать',
                    'url' => '#contact'
                ]
            ],
            [
                'name' => 'Стандарт',
                'price' => '₽35,000',
                'period' => 'месяц',
                'description' => 'Для опытных водителей',
                'featured' => true,
                'features' => [
                    'Аренда квадроцикла на 3 дня',
                    'Полная экипировка',
                    'Инструктаж по безопасности',
                    'Страховка',
                    'Гид по маршрутам',
                    'Фотосессия'
                ],
                'button' => [
                    'text' => 'Заказать',
                    'url' => '#contact'
                ]
            ],
            [
                'name' => 'Премиум',
                'price' => '₽65,000',
                'period' => 'месяц',
                'description' => 'Для профессионалов',
                'features' => [
                    'Аренда квадроцикла на неделю',
                    'Премиум экипировка',
                    'Персональный инструктор',
                    'Расширенная страховка',
                    'Эксклюзивные маршруты',
                    'Профессиональная фотосессия',
                    'Трансфер до места'
                ],
                'button' => [
                    'text' => 'Заказать',
                    'url' => '#contact'
                ]
            ]
        ]
    ])

    {{-- Отзывы --}}
    @include('components.landing.testimonials', [
        'title' => 'Отзывы наших клиентов',
        'subtitle' => 'Что говорят о нас наши клиенты',
        'testimonials' => [
            [
                'name' => 'Алексей Петров',
                'position' => 'Владелец квадроцикла',
                'text' => 'Отличный сервис! Квадроцикл приехал в идеальном состоянии, доставка была быстрой. Рекомендую всем!',
                'rating' => 5,
                'avatar' => '/images/avatar1.jpg'
            ],
            [
                'name' => 'Мария Сидорова',
                'position' => 'Любитель активного отдыха',
                'text' => 'Первый раз каталась на квадроцикле. Инструктор был очень терпеливым и профессиональным. Осталась в восторге!',
                'rating' => 5,
                'avatar' => '/images/avatar2.jpg'
            ],
            [
                'name' => 'Дмитрий Козлов',
                'position' => 'Предприниматель',
                'text' => 'Заказывал квадроцикл для корпоратива. Все прошло на высшем уровне. Сотрудники остались довольны!',
                'rating' => 5,
                'avatar' => '/images/avatar3.jpg'
            ]
        ]
    ])

    {{-- Call-to-Action --}}
    @include('components.landing.cta', [
        'title' => 'Готовы к приключениям?',
        'subtitle' => 'Закажите квадроцикл прямо сейчас и получите скидку 10% на первый заказ!',
        'buttonText' => 'Заказать со скидкой',
        'buttonUrl' => '#contact',
        'backgroundClass' => 'bg-primary',
        'textClass' => 'text-white'
    ])

    {{-- Форма обратной связи --}}
    @include('components.landing.feedback-form', [
        'title' => 'Свяжитесь с нами',
        'subtitle' => 'Оставьте заявку и мы свяжемся с вами в течение 15 минут'
    ])
@endsection