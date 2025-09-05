@props(['src', 'alt' => '', 'class' => '', 'size' => '50px'])

<div class="image-preview-container {{ $class }}">
    <img src="{{ $src }}" 
         alt="{{ $alt }}" 
         class="img-thumbnail cursor-pointer" 
         style="width: {{ $size }}; height: {{ $size }}; object-fit: cover;"
         onclick="ImagePreview.show('{{ $src }}', '{{ $alt }}')"
         title="Нажмите для увеличения">
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}
</style>
