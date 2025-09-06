{{-- Блок преимуществ/особенностей --}}
@php
    $title = $title ?? 'Наши преимущества';
    $subtitle = $subtitle ?? '';
    $features = $features ?? [];
    $columns = $columns ?? 3;
    $showIcons = $showIcons ?? true;
    $animation = $animation ?? true;
@endphp

<section class="py-5">
    <div class="container">
        @if($title || $subtitle)
            <div class="text-center mb-5">
                @if($title)
                    <h2 class="display-5 fw-bold mb-3">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="lead text-muted">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        @if(!empty($features))
            <div class="row g-4">
                @foreach($features as $index => $feature)
                    <div class="col-lg-{{ 12 / $columns }} col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm feature-card {{ $animation ? 'animate-on-scroll' : '' }}" 
                             data-aos="fade-up" 
                             data-aos-delay="{{ $index * 100 }}">
                            <div class="card-body text-center p-4">
                                @if($showIcons && isset($feature['icon']))
                                    <div class="feature-icon mb-3">
                                        @if(str_starts_with($feature['icon'], 'fa-'))
                                            <i class="fas {{ $feature['icon'] }} fa-3x text-primary"></i>
                                        @elseif(str_starts_with($feature['icon'], 'bi-'))
                                            <i class="bi {{ $feature['icon'] }} fs-1 text-primary"></i>
                                        @else
                                            <i class="fas fa-check-circle fa-3x text-primary"></i>
                                        @endif
                                    </div>
                                @endif

                                @if(isset($feature['title']))
                                    <h5 class="card-title fw-bold mb-3">{{ $feature['title'] }}</h5>
                                @endif

                                @if(isset($feature['description']))
                                    <p class="card-text text-muted">{{ $feature['description'] }}</p>
                                @endif

                                @if(isset($feature['button']))
                                    <div class="mt-3">
                                        <a href="{{ $feature['button']['url'] ?? '#' }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            {{ $feature['button']['text'] ?? 'Подробнее' }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Пример данных по умолчанию --}}
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-rocket fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Быстро</h5>
                            <p class="card-text text-muted">Быстрая загрузка и отзывчивый интерфейс</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-shield-alt fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Безопасно</h5>
                            <p class="card-text text-muted">Защита данных и безопасные транзакции</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-mobile-alt fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Мобильно</h5>
                            <p class="card-text text-muted">Адаптивный дизайн для всех устройств</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
/* Стили для компонента features */
.feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.feature-icon {
    transition: transform 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1);
}

/* Анимации при скролле */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.animate-on-scroll.animated {
    opacity: 1;
    transform: translateY(0);
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .feature-card {
        margin-bottom: 1rem;
    }
    
    .feature-icon i {
        font-size: 2rem !important;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}

@media (max-width: 576px) {
    .feature-icon i {
        font-size: 1.5rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}
</style>

@if($animation)
<script>
// Простая анимация при скролле без внешних библиотек
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endif
