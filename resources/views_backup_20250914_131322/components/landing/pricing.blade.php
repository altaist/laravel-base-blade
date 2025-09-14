{{-- Тарифы/цены --}}
@php
    $title = $title ?? 'Наши тарифы';
    $subtitle = $subtitle ?? '';
    $plans = $plans ?? [];
    $columns = $columns ?? 3;
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

        @if(!empty($plans))
            <div class="row g-4 justify-content-center">
                @foreach($plans as $index => $plan)
                    <div class="col-lg-{{ 12 / $columns }} col-md-6">
                        <div class="card h-100 border-0 shadow-sm pricing-card {{ isset($plan['featured']) && $plan['featured'] ? 'featured' : '' }} d-flex flex-column">
                            @if(isset($plan['featured']) && $plan['featured'])
                                <div class="badge bg-primary position-absolute top-0 start-50 translate-middle px-3 py-2">
                                    Популярный
                                </div>
                            @endif
                            
                            <div class="card-body text-center p-4 d-flex flex-column flex-grow-1">
                                @if(isset($plan['name']))
                                    <h5 class="card-title fw-bold mb-3">{{ $plan['name'] }}</h5>
                                @endif

                                @if(isset($plan['price']))
                                    <div class="pricing-price mb-4">
                                        <span class="display-4 fw-bold text-primary">{{ $plan['price'] }}</span>
                                        @if(isset($plan['period']))
                                            <span class="text-muted">/ {{ $plan['period'] }}</span>
                                        @endif
                                    </div>
                                @endif

                                @if(isset($plan['description']))
                                    <p class="text-muted mb-4">{{ $plan['description'] }}</p>
                                @endif

                                @if(isset($plan['features']) && is_array($plan['features']))
                                    <ul class="list-unstyled mb-4 flex-grow-1">
                                        @foreach($plan['features'] as $feature)
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if(isset($plan['button']))
                                    <div class="d-grid mt-auto">
                                        <a href="{{ $plan['button']['url'] ?? '#' }}" 
                                           class="btn {{ isset($plan['featured']) && $plan['featured'] ? 'btn-primary' : 'btn-outline-primary' }} btn-lg">
                                            {{ $plan['button']['text'] ?? 'Выбрать план' }}
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
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm d-flex flex-column">
                        <div class="card-body text-center p-4 d-flex flex-column flex-grow-1">
                            <h5 class="card-title fw-bold mb-3">Базовый</h5>
                            <div class="pricing-price mb-4">
                                <span class="display-4 fw-bold text-primary">₽1,000</span>
                                <span class="text-muted">/ месяц</span>
                            </div>
                            <p class="text-muted mb-4">Идеально для начинающих</p>
                            <ul class="list-unstyled mb-4 flex-grow-1">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>До 5 проектов</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Базовая поддержка</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>5GB хранилища</li>
                            </ul>
                            <div class="d-grid mt-auto">
                                <a href="#" class="btn btn-outline-primary btn-lg">Выбрать план</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm featured d-flex flex-column">
                        <div class="badge bg-primary position-absolute top-0 start-50 translate-middle px-3 py-2">
                            Популярный
                        </div>
                        <div class="card-body text-center p-4 d-flex flex-column flex-grow-1">
                            <h5 class="card-title fw-bold mb-3">Профессиональный</h5>
                            <div class="pricing-price mb-4">
                                <span class="display-4 fw-bold text-primary">₽2,500</span>
                                <span class="text-muted">/ месяц</span>
                            </div>
                            <p class="text-muted mb-4">Для растущего бизнеса</p>
                            <ul class="list-unstyled mb-4 flex-grow-1">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>До 25 проектов</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Приоритетная поддержка</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>50GB хранилища</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Аналитика</li>
                            </ul>
                            <div class="d-grid mt-auto">
                                <a href="#" class="btn btn-primary btn-lg">Выбрать план</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm d-flex flex-column">
                        <div class="card-body text-center p-4 d-flex flex-column flex-grow-1">
                            <h5 class="card-title fw-bold mb-3">Корпоративный</h5>
                            <div class="pricing-price mb-4">
                                <span class="display-4 fw-bold text-primary">₽5,000</span>
                                <span class="text-muted">/ месяц</span>
                            </div>
                            <p class="text-muted mb-4">Для крупных компаний</p>
                            <ul class="list-unstyled mb-4 flex-grow-1">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Безлимитные проекты</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>24/7 поддержка</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>500GB хранилища</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Персональный менеджер</li>
                            </ul>
                            <div class="d-grid mt-auto">
                                <a href="#" class="btn btn-outline-primary btn-lg">Выбрать план</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.pricing-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.pricing-card.featured {
    border: 2px solid #0d6efd;
    transform: scale(1.05);
}

.pricing-card.featured:hover {
    transform: scale(1.05) translateY(-10px);
}

.pricing-price {
    position: relative;
}

.badge {
    font-size: 0.8rem;
    border-radius: 20px;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .pricing-card.featured {
        transform: none;
        margin-bottom: 2rem;
    }
    
    .pricing-card.featured:hover {
        transform: translateY(-5px);
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
}

@media (max-width: 576px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .card-body {
        padding: 2rem !important;
    }
}
</style>
