@extends('layouts.app')

@section('content')
<div class="page">
    <div class="container">
        {{-- Hero секция в стиле page --}}
        <div class="row mb-5" style="margin-top: 1rem;">
            <div class="col-12">
                <div class="hero-content" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); position: relative; overflow: hidden; border-radius: 15px;">
                    <div class="hero-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.2); z-index: 1;"></div>
                    <div class="container py-5" style="position: relative; z-index: 2;">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-6">
                                <div class="text-white">
                                    <h1 class="display-4 fw-bold mb-4">Добро пожаловать в Kvadro!</h1>
                                    <p class="lead mb-4">Лучшие квадроциклы и аксессуары для ваших приключений. Качество, надежность и страсть к движению.</p>
                                    <a href="#catalog" class="btn btn-light btn-lg">
                                        Смотреть каталог
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6 text-center">
                                <div class="hero-image-container">
                                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=400&fit=crop" 
                                         alt="Квадроцикл" 
                                         class="img-fluid rounded shadow hero-image">
                                    <div class="hero-image-overlay"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        {{-- Преимущества в карточках --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="section-title">Почему выбирают нас</h2>
                    <p class="section-subtitle">Мы предлагаем только лучшее для ваших приключений</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card h-100">
                            <div class="feature-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <h5 class="feature-title">Быстрая доставка</h5>
                            <p class="feature-description">Доставляем квадроциклы по всей России в течение 3-7 дней</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card h-100">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="feature-title">Гарантия качества</h5>
                            <p class="feature-description">Официальная гарантия на все модели от производителя</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card h-100">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h5 class="feature-title">Поддержка 24/7</h5>
                            <p class="feature-description">Круглосуточная техническая поддержка и консультации</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Статьи в карточках --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="section-title">Последние статьи</h2>
                    <p class="section-subtitle">Читайте наши новейшие публикации о квадроциклах и приключениях</p>
                </div>
                @if($articles->count() > 0)
                    <div class="row">
                        @foreach($articles as $article)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <x-articles.article-card :article="$article" />
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-12 text-center mt-4">
                            <a href="#articles" class="btn btn-outline-primary">
                                Все статьи
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">Статьи не найдены</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Галерея в карточке --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="section-title">Наши квадроциклы</h2>
                    <p class="section-subtitle">Выберите идеальную модель для ваших приключений</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="gallery-card h-100">
                            <div class="gallery-image-container">
                                <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop" 
                                     alt="Спортивный квадроцикл">
                                <div class="gallery-overlay">
                                    <h5 class="gallery-title">Спортивная модель</h5>
                                    <p class="gallery-description">Высокая скорость и маневренность</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="gallery-card h-100">
                            <div class="gallery-image-container">
                                <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop" 
                                     alt="Утилитарный квадроцикл">
                                <div class="gallery-overlay">
                                    <h5 class="gallery-title">Утилитарная модель</h5>
                                    <p class="gallery-description">Надежность для работы и отдыха</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="gallery-card h-100">
                            <div class="gallery-image-container">
                                <img src="https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?w=400&h=300&fit=crop" 
                                     alt="Детский квадроцикл">
                                <div class="gallery-overlay">
                                    <h5 class="gallery-title">Детская модель</h5>
                                    <p class="gallery-description">Безопасность для маленьких водителей</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Тарифы в карточках --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="section-title">Наши услуги</h2>
                    <p class="section-subtitle">Выберите подходящий пакет услуг</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card h-100">
                            <div class="pricing-header">
                                <h5 class="pricing-title">Базовый</h5>
                                <div class="pricing-price">
                                    <span class="price-amount">₽15,000</span>
                                    <span class="price-period">/ месяц</span>
                                </div>
                                <p class="pricing-description">Для начинающих райдеров</p>
                            </div>
                            <div class="pricing-features">
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i>Аренда квадроцикла на 1 день</li>
                                    <li><i class="fas fa-check"></i>Базовая экипировка</li>
                                    <li><i class="fas fa-check"></i>Инструктаж по безопасности</li>
                                    <li><i class="fas fa-check"></i>Страховка</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <a href="#contact" class="btn btn-outline-primary">Заказать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card h-100 featured">
                            <div class="pricing-badge">Популярный</div>
                            <div class="pricing-header">
                                <h5 class="pricing-title">Стандарт</h5>
                                <div class="pricing-price">
                                    <span class="price-amount">₽35,000</span>
                                    <span class="price-period">/ месяц</span>
                                </div>
                                <p class="pricing-description">Для опытных водителей</p>
                            </div>
                            <div class="pricing-features">
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i>Аренда квадроцикла на 3 дня</li>
                                    <li><i class="fas fa-check"></i>Полная экипировка</li>
                                    <li><i class="fas fa-check"></i>Инструктаж по безопасности</li>
                                    <li><i class="fas fa-check"></i>Страховка</li>
                                    <li><i class="fas fa-check"></i>Гид по маршрутам</li>
                                    <li><i class="fas fa-check"></i>Фотосессия</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <a href="#contact" class="btn btn-primary">Заказать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card h-100">
                            <div class="pricing-header">
                                <h5 class="pricing-title">Премиум</h5>
                                <div class="pricing-price">
                                    <span class="price-amount">₽65,000</span>
                                    <span class="price-period">/ месяц</span>
                                </div>
                                <p class="pricing-description">Для профессионалов</p>
                            </div>
                            <div class="pricing-features">
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i>Аренда квадроцикла на неделю</li>
                                    <li><i class="fas fa-check"></i>Премиум экипировка</li>
                                    <li><i class="fas fa-check"></i>Персональный инструктор</li>
                                    <li><i class="fas fa-check"></i>Расширенная страховка</li>
                                    <li><i class="fas fa-check"></i>Эксклюзивные маршруты</li>
                                    <li><i class="fas fa-check"></i>Профессиональная фотосессия</li>
                                    <li><i class="fas fa-check"></i>Трансфер до места</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <a href="#contact" class="btn btn-outline-primary">Заказать</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        {{-- Call-to-Action в карточке --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="cta-section text-center py-5">
                    <div class="container">
                        <h2 class="cta-title">Готовы к приключениям?</h2>
                        <p class="cta-subtitle">Закажите квадроцикл прямо сейчас и получите скидку 10% на первый заказ!</p>
                        <a href="#contact" class="btn btn-primary btn-lg">
                            Заказать со скидкой
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Форма обратной связи в карточке --}}
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="section-title">Свяжитесь с нами</h2>
                    <p class="section-subtitle">Оставьте заявку и мы свяжемся с вами в течение 15 минут</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <form action="{{ route('feedback.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-bold">Имя *</label>
                                                <input type="text" 
                                                       class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" 
                                                       name="name" 
                                                       value="{{ old('name') }}"
                                                       placeholder="Введите ваше имя"
                                                       required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="contact" class="form-label fw-bold">Контакт *</label>
                                                <input type="text" 
                                                       class="form-control @error('contact') is-invalid @enderror" 
                                                       id="contact" 
                                                       name="contact" 
                                                       value="{{ old('contact') }}"
                                                       placeholder="Email или телефон"
                                                       required>
                                                @error('contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="comment" class="form-label fw-bold">Сообщение *</label>
                                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                                  id="comment" 
                                                  name="comment" 
                                                  rows="5" 
                                                  placeholder="Опишите ваш вопрос или предложение"
                                                  required>{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-paper-plane me-2"></i>Отправить заявку
                                        </button>
                                    </div>

                                    @if(session('success'))
                                        <div class="alert alert-success mt-3" role="alert">
                                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Чистый современный дизайн для home2 */
.page {
    padding: 1rem 0;
}

/* Заголовки секций */
.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

/* Hero секция */
.hero-card {
    border-radius: 20px;
    overflow: hidden;
}

/* Отступы для текста в hero */
.hero-content .text-white {
    padding-left: 2rem;
}

/* Мобильная адаптация для hero */
@media (max-width: 768px) {
    .hero-content .btn {
        margin-bottom: 2rem;
    }
    
    .hero-content .text-white {
        padding-left: 1.5rem;
    }
}

@media (max-width: 576px) {
    .hero-content .text-white {
        padding-left: 1rem;
    }
}

/* Карточки преимуществ */
.feature-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.feature-icon i {
    font-size: 2rem;
    color: #007bff;
}

.feature-card:hover .feature-icon {
    background: #007bff;
    transform: scale(1.1);
}

.feature-card:hover .feature-icon i {
    color: white;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.feature-description {
    color: #6c757d;
    line-height: 1.6;
    flex-grow: 1;
}

/* Галерея */
.gallery-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.gallery-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.gallery-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-card:hover .gallery-image-container img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    padding: 2rem 1.5rem 1.5rem;
    color: white;
}

.gallery-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.gallery-description {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
}

/* Тарифы */
.pricing-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.pricing-card.featured {
    border: 2px solid #007bff;
    transform: scale(1.02);
}

.pricing-card.featured:hover {
    transform: scale(1.02) translateY(-5px);
}

.pricing-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #007bff;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.pricing-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.pricing-price {
    margin-bottom: 1rem;
}

.price-amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: #007bff;
}

.price-period {
    color: #6c757d;
    font-size: 1rem;
}

.pricing-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
    flex-grow: 1;
}

.feature-list li {
    padding: 0.5rem 0;
    color: #6c757d;
    display: flex;
    align-items: center;
}

.feature-list i {
    color: #28a745;
    margin-right: 0.5rem;
    font-size: 0.9rem;
}

.pricing-action {
    margin-top: auto;
}

/* Отзывы */
.testimonial-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.testimonial-avatar {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.testimonial-avatar i {
    font-size: 2rem;
    color: #007bff;
}

.testimonial-quote {
    font-style: italic;
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}

.author-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.author-title {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.testimonial-rating {
    color: #ffc107;
}

/* CTA секция */
.cta-section {
    background: #007bff;
    color: white;
    border-radius: 15px;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-subtitle {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

/* Стили для статей */
.article-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.article-card-link-wrapper {
    text-decoration: none;
    color: inherit;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.article-card-link-wrapper:hover {
    text-decoration: none;
    color: inherit;
}

.article-card-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.article-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.article-card:hover .article-card-image img {
    transform: scale(1.05);
}

.article-card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    padding: 20px 15px 15px;
}

.article-card-title {
    color: white;
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.article-card-content {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.article-card-description {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 0;
    flex-grow: 1;
}

.article-card-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    position: relative;
    z-index: 10;
}

.article-card-reactions {
    display: flex;
    gap: 10px;
}

.reaction-btn {
    background: none;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    color: #6c757d;
    position: relative;
    z-index: 20;
}

.reaction-btn:hover {
    border-color: #007bff;
    color: #007bff;
    transform: translateY(-1px);
}

.reaction-btn.active {
    border-color: #007bff;
    background-color: #007bff;
    color: white;
}

.reaction-btn.active.like-btn {
    border-color: #dc3545;
    background-color: #dc3545;
}

.reaction-btn.active.favorite-btn {
    border-color: #ffc107;
    background-color: #ffc107;
    color: #212529;
}

.reaction-btn i {
    font-size: 0.8rem;
}

.reaction-count {
    font-weight: 600;
    font-size: 0.8rem;
}

.article-card-read-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    z-index: 20;
}

.article-card-read-link .btn-text {
    display: inline;
}

.article-card-read-link:hover {
    background: #0056b3;
    transform: translateX(5px);
    color: white;
}

.article-card-read-link i {
    font-size: 0.8rem;
    transition: transform 0.3s ease;
}

.article-card-read-link:hover i {
    transform: translateX(3px);
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .section-title {
        font-size: 2rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
    
    .pricing-card.featured {
        transform: none;
        margin-bottom: 2rem;
    }
    
    .pricing-card.featured:hover {
        transform: translateY(-5px);
    }
    
    .article-card-image {
        height: 180px;
    }
    
    .article-card-title {
        font-size: 1.1rem;
    }
    
    .article-card-read-link .btn-text {
        display: none;
    }
    
    .article-card-read-link {
        padding: 0.75rem;
        border-radius: 6px;
    }
    
    /* Меньший отступ между header и hero на мобильных */
    .page {
        padding: 0.5rem 0 !important;
    }
    
    .row[style*="margin-top: 1rem"] {
        margin-top: 0.25rem !important;
    }
    
    /* Мобильная версия hero - красивая картинка с overlay */
    .hero-content {
        background: none !important;
        padding: 0 !important;
        position: relative;
    }
    
    .hero-content .container {
        padding: 0 !important;
    }
    
    .hero-content .row {
        position: relative;
        margin: 0 !important;
    }
    
    .hero-content .row .col-lg-6:last-child {
        width: 100% !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
        position: relative;
        padding: 0 !important;
    }
    
    .hero-image-container {
        position: relative;
        width: 100%;
        height: 500px;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .hero-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .hero-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2;
    }
    
    .hero-content .row .col-lg-6:first-child {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        z-index: 3;
        width: 90%;
        text-align: left;
        padding: 1.5rem 1rem;
        margin-left: 0.5rem;
    }
    
    .hero-content .text-white h1 {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        line-height: 1.2;
        color: white !important;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }
    
    .hero-content .text-white .lead {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        line-height: 1.4;
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
    }
    
    .hero-content .text-white .btn {
        font-size: 1.1rem;
        padding: 1rem 2rem;
        background: white !important;
        color: #2c3e50 !important;
        border: none;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        border-radius: 8px;
    }
}

@media (max-width: 576px) {
    .section-title {
        font-size: 1.75rem;
    }
    
    .cta-title {
        font-size: 1.75rem;
    }
    
    .article-card-content {
        padding: 15px;
    }
    
    .reaction-btn {
        padding: 6px 10px;
        font-size: 0.8rem;
    }
}
</style>
@endsection
