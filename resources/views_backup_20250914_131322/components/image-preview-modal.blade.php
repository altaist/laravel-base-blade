{{-- 
Компонент для добавления превью к любому изображению
Использование: 
<x-image-preview-modal>
    <img src="path/to/image.jpg" alt="Описание" class="your-classes">
</x-image-preview-modal>
--}}

<div class="image-preview-wrapper">
    {{ $slot }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Находим все изображения внутри этого компонента
    const wrapper = document.currentScript.previousElementSibling;
    const images = wrapper.querySelectorAll('img');
    
    images.forEach(function(img) {
        // Добавляем курсор и обработчик клика
        img.style.cursor = 'pointer';
        img.title = img.title || 'Нажмите для увеличения';
        
        img.addEventListener('click', function() {
            ImagePreview.show(this.src, this.alt);
        });
    });
});
</script>
