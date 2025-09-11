export class PageGallery {
    constructor() {
        this.gallery = document.querySelector('#pageGallery');
        if (!this.gallery) return;
        
        this.indicators = document.querySelectorAll('.gallery-indicators .indicator');
        this.init();
    }

    init() {
        // Обработка индикаторов
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                this.setActiveIndicator(index);
                // Используем Bootstrap Carousel API
                bootstrap.Carousel.getInstance(this.gallery).to(index);
            });
        });

        // Обновление индикаторов при смене слайда
        this.gallery.addEventListener('slide.bs.carousel', (e) => {
            this.setActiveIndicator(e.to);
        });

        // Обработка зума изображений, если есть
        const zoomableImages = this.gallery.querySelectorAll('.image-overlay');
        zoomableImages.forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                const img = e.currentTarget.previousElementSibling;
                if (img) {
                    this.openImageViewer(img.src);
                }
            });
        });
    }

    setActiveIndicator(index) {
        this.indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });
    }

    openImageViewer(src) {
        // Здесь можно добавить логику для просмотра изображений в полном размере
        // Например, используя lightbox библиотеку
        if (typeof Lightbox !== 'undefined') {
            Lightbox.show(src);
        } else {
            window.open(src, '_blank');
        }
    }
}
