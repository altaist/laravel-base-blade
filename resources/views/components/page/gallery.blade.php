@props(['images', 'zoom' => false])

<div class="article-gallery-modern mt-5">
    <div class="gallery-header">
        <h3 class="gallery-title">Галерея</h3>
        <div class="gallery-indicators">
            @foreach($images as $index => $image)
                <span class="indicator {{ $loop->first ? 'active' : '' }}" data-slide="{{ $index }}"></span>
            @endforeach
        </div>
    </div>
    
    <div class="gallery-container">
        <div id="pageGallery" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($images as $image)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <div class="gallery-image-container">
                            <img src="{{ $image }}" class="gallery-image" alt="Изображение {{ $loop->iteration }}">
                            @if($zoom)
                                <div class="image-overlay">
                                    <div class="image-overlay-content">
                                        <i class="fas fa-expand-arrows-alt"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev-modern" type="button" data-bs-target="#pageGallery" data-bs-slide="prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-control-next-modern" type="button" data-bs-target="#pageGallery" data-bs-slide="next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
