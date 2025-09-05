/**
 * Глобальный модуль для превью изображений
 * Использование: ImagePreview.init() для инициализации
 */
window.ImagePreview = (function() {
    let modal = null;
    let previewImage = null;
    let isInitialized = false;

    /**
     * Инициализация модуля
     */
    function init() {
        if (isInitialized) return;

        // Создаем модальное окно если его нет
        createModal();
        
        // Добавляем обработчики событий
        addEventListeners();
        
        isInitialized = true;
    }

    /**
     * Создание модального окна
     */
    function createModal() {
        // Проверяем, есть ли уже модальное окно
        if (document.getElementById('imagePreviewModal')) {
            modal = document.getElementById('imagePreviewModal');
            previewImage = document.getElementById('previewImage');
            return;
        }

        // Создаем модальное окно
        const modalHTML = `
            <div id="imagePreviewModal" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content bg-dark">
                        <div class="modal-header border-0 position-absolute top-0 end-0" style="z-index: 1055;">
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body d-flex align-items-center justify-content-center p-0">
                            <img id="previewImage" src="" alt="" class="img-fluid" style="max-height: 100vh; max-width: 100vw; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Добавляем стили если их нет
        if (!document.getElementById('imagePreviewStyles')) {
            const styles = document.createElement('style');
            styles.id = 'imagePreviewStyles';
            styles.textContent = `
                .modal-fullscreen .modal-content {
                    border: none;
                    border-radius: 0;
                }
                #previewImage {
                    transition: transform 0.3s ease;
                }
                #previewImage:hover {
                    transform: scale(1.02);
                }
            `;
            document.head.appendChild(styles);
        }

        // Добавляем модальное окно в body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        modal = document.getElementById('imagePreviewModal');
        previewImage = document.getElementById('previewImage');
    }

    /**
     * Добавление обработчиков событий
     */
    function addEventListeners() {
        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hide();
            }
        });

        // Закрытие по клику на фон
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hide();
                }
            });
        }
    }

    /**
     * Открытие превью изображения
     * @param {string} src - URL изображения
     * @param {string} alt - Альтернативный текст
     */
    function show(src, alt = '') {
        if (!isInitialized) {
            init();
        }

        if (!modal || !previewImage) {
            console.error('ImagePreview: Modal not initialized');
            return;
        }

        previewImage.src = src;
        previewImage.alt = alt;
        
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    /**
     * Закрытие превью
     */
    function hide() {
        if (!modal) return;
        
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    }

    /**
     * Проверка инициализации
     */
    function isReady() {
        return isInitialized;
    }

    // Публичный API
    return {
        init: init,
        show: show,
        hide: hide,
        isReady: isReady
    };
})();

// Автоматическая инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    ImagePreview.init();
});
