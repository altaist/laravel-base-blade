{{-- Отзывы клиентов --}}
@php
    $title = $title ?? 'Отзывы наших клиентов';
    $subtitle = $subtitle ?? '';
    $testimonials = $testimonials ?? [];
    $showArrows = $showArrows ?? true;
    $autoplay = $autoplay ?? true;
    $interval = $interval ?? 6000;
@endphp

<section class="py-5 bg-light">
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

        @if(!empty($testimonials))
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="{{ $autoplay ? 'carousel' : 'false' }}" data-bs-interval="{{ $interval }}">
                <div class="carousel-inner">
                    @foreach($testimonials as $index => $testimonial)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm testimonial-card">
                                        <div class="card-body text-center p-5">
                                            @if(isset($testimonial['avatar']))
                                                <img src="{{ $testimonial['avatar'] }}" 
                                                     alt="{{ $testimonial['name'] ?? 'Клиент' }}" 
                                                     class="rounded-circle mb-3" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" 
                                                     style="width: 80px; height: 80px;">
                                                    <i class="fas fa-user text-white fa-2x"></i>
                                                </div>
                                            @endif

                                            @if(isset($testimonial['text']))
                                                <blockquote class="blockquote mb-4">
                                                    <p class="mb-0 fs-5 fst-italic">"{{ $testimonial['text'] }}"</p>
                                                </blockquote>
                                            @endif

                                            @if(isset($testimonial['name']))
                                                <h5 class="card-title mb-1">{{ $testimonial['name'] }}</h5>
                                            @endif

                                            @if(isset($testimonial['position']))
                                                <p class="text-muted mb-0">{{ $testimonial['position'] }}</p>
                                            @endif

                                            @if(isset($testimonial['rating']))
                                                <div class="mt-3">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($showArrows && count($testimonials) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" style="background-color: rgba(0,0,0,0.3); border-radius: 50%; width: 50px; height: 50px;"></span>
                        <span class="visually-hidden">Предыдущий</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" style="background-color: rgba(0,0,0,0.3); border-radius: 50%; width: 50px; height: 50px;"></span>
                        <span class="visually-hidden">Следующий</span>
                    </button>
                @endif

                @if(count($testimonials) > 1)
                    <div class="carousel-indicators">
                        @foreach($testimonials as $index => $testimonial)
                            <button type="button" 
                                    data-bs-target="#testimonialsCarousel" 
                                    data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}"
                                    style="background-color: rgba(0,0,0,0.5);"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            {{-- Пример данных по умолчанию --}}
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-user text-white fa-2x"></i>
                            </div>
                            <blockquote class="blockquote mb-4">
                                <p class="mb-0 fs-5 fst-italic">"Отличный сервис! Рекомендую всем."</p>
                            </blockquote>
                            <h5 class="card-title mb-1">Иван Петров</h5>
                            <p class="text-muted mb-0">Клиент</p>
                            <div class="mt-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.testimonial-card {
    border-radius: 20px;
    transition: transform 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-5px);
}

.carousel-control-prev,
.carousel-control-next {
    width: 5%;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-size: 20px 20px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 5px;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .card-body {
        padding: 3rem !important;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 10%;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 15px 15px;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 2rem !important;
    }
    
    .rounded-circle {
        width: 60px !important;
        height: 60px !important;
    }
    
    .fas.fa-user {
        font-size: 1.5rem !important;
    }
}
</style>
