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
                                                <div class="gallery-image-container" style="height: 250px; position: relative; overflow: hidden;">
                                                    @if(isset($img['url']) && $img['url'])
                                                        <img src="{{ $img['url'] }}" 
                                                             class="card-img-top" 
                                                             alt="{{ $img['alt'] ?? 'Изображение' }}"
                                                             style="height: 100%; width: 100%; object-fit: cover;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    @endif
                                                    <div class="gallery-placeholder {{ isset($img['url']) && $img['url'] ? 'd-none' : '' }}" 
                                                         style="height: 100%; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); color: #6c757d;">
                                                        <i class="fas fa-image fa-3x mb-3"></i>
                                                        <span class="fw-bold">{{ $img['title'] ?? 'Изображение' }}</span>
                                                        @if(isset($img['description']))
                                                            <small class="text-muted mt-1">{{ $img['description'] }}</small>
                                                        @endif
                                                    </div>
                                                </div>
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
                                        <div class="gallery-image-container" style="height: 400px; position: relative; overflow: hidden; border-radius: 0.5rem;">
                                            @if(isset($image['url']) && $image['url'])
                                                <img src="{{ $image['url'] }}" 
                                                     class="d-block w-100" 
                                                     alt="{{ $image['alt'] ?? 'Изображение' }}"
                                                     style="height: 100%; width: 100%; object-fit: cover;"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            @endif
                                            <div class="gallery-placeholder {{ isset($image['url']) && $image['url'] ? 'd-none' : '' }}" 
                                                 style="height: 100%; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); color: #6c757d;">
                                                <i class="fas fa-image fa-4x mb-4"></i>
                                                <span class="fw-bold fs-4">{{ $image['title'] ?? 'Изображение' }}</span>
                                                @if(isset($image['description']))
                                                    <small class="text-muted mt-2">{{ $image['description'] }}</small>
                                                @endif
                                            </div>
                                        </div>
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

/* Стили для placeholder'ов */
.gallery-placeholder {
    transition: all 0.3s ease;
}

.gallery-placeholder:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%) !important;
    transform: scale(1.02);
}

.gallery-image-container {
    border-radius: 0.5rem;
    overflow: hidden;
}

.gallery-image-container img {
    transition: transform 0.3s ease;
}

.gallery-image-container:hover img {
    transform: scale(1.05);
}
</style>
