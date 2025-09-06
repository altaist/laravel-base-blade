{{-- Галерея с полупрозрачными стрелками --}}
@php
    $title = $title ?? 'Галерея';
    $images = $images ?? [];
    $showArrows = $showArrows ?? true;
    $autoplay = $autoplay ?? true;
    $interval = $interval ?? 5000;
@endphp

<section class="py-5 bg-light">
    <div class="container">
        @if($title)
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">{{ $title }}</h2>
            </div>
        @endif

        @if(!empty($images))
            <div id="galleryCarousel" class="carousel slide" data-bs-ride="{{ $autoplay ? 'carousel' : 'false' }}" data-bs-interval="{{ $interval }}">
                <div class="carousel-inner">
                    @foreach($images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row g-3">
                                @if(is_array($image))
                                    {{-- Если передано несколько изображений для одного слайда --}}
                                    @foreach($image as $img)
                                        <div class="col-md-4">
                                            <div class="card h-100 shadow-sm">
                                                <img src="{{ $img['url'] ?? $img }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $img['alt'] ?? 'Изображение' }}"
                                                     style="height: 250px; object-fit: cover;">
                                                @if(isset($img['title']))
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $img['title'] }}</h5>
                                                        @if(isset($img['description']))
                                                            <p class="card-text">{{ $img['description'] }}</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Если передано одно изображение --}}
                                    <div class="col-12">
                                        <img src="{{ $image['url'] ?? $image }}" 
                                             class="d-block w-100 rounded" 
                                             alt="{{ $image['alt'] ?? 'Изображение' }}"
                                             style="height: 400px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($showArrows && count($images) > 1)
                    {{-- Полупрозрачные стрелки --}}
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" style="background-color: rgba(0,0,0,0.3); border-radius: 50%; width: 50px; height: 50px;"></span>
                        <span class="visually-hidden">Предыдущий</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" style="background-color: rgba(0,0,0,0.3); border-radius: 50%; width: 50px; height: 50px;"></span>
                        <span class="visually-hidden">Следующий</span>
                    </button>
                @endif

                @if(count($images) > 1)
                    {{-- Индикаторы --}}
                    <div class="carousel-indicators">
                        @foreach($images as $index => $image)
                            <button type="button" 
                                    data-bs-target="#galleryCarousel" 
                                    data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}"
                                    style="background-color: rgba(0,0,0,0.5);"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">Изображения не найдены</p>
            </div>
        @endif
    </div>
</section>

<style>
/* Дополнительные стили для галереи */
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
    .carousel-control-prev,
    .carousel-control-next {
        width: 10%;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 15px 15px;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
    }
    
    .card-img-top {
        height: 200px !important;
    }
    
    .d-block.w-100 {
        height: 300px !important;
    }
}

@media (max-width: 576px) {
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 12px 12px;
        border-radius: 50% !important;
        width: 35px !important;
        height: 35px !important;
    }
}
</style>
