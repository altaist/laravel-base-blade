{{-- Переиспользуемый компонент кнопки --}}
@php
    $type = $type ?? 'button';
    $variant = $variant ?? 'primary';
    $size = $size ?? 'md';
    $outline = $outline ?? false;
    $block = $block ?? false;
    $disabled = $disabled ?? false;
    $icon = $icon ?? null;
    $iconPosition = $iconPosition ?? 'left';
    $class = $class ?? '';
@endphp

@php
    $buttonClass = 'btn';
    
    // Размер
    switch($size) {
        case 'sm': $buttonClass .= ' btn-sm'; break;
        case 'lg': $buttonClass .= ' btn-lg'; break;
        case 'xl': $buttonClass .= ' btn-lg px-4 py-3'; break;
    }
    
    // Вариант
    if($outline) {
        $buttonClass .= ' btn-outline-' . $variant;
    } else {
        $buttonClass .= ' btn-' . $variant;
    }
    
    // Блочная кнопка
    if($block) {
        $buttonClass .= ' w-100';
    }
    
    // Дополнительные классы
    if($class) {
        $buttonClass .= ' ' . $class;
    }
@endphp

<{{ $tag ?? 'button' }} 
    type="{{ $type }}"
    class="{{ $buttonClass }}"
    @if($disabled) disabled @endif
    @if(isset($href)) href="{{ $href }}" @endif
    @if(isset($onclick)) onclick="{{ $onclick }}" @endif
    @if(isset($dataAttributes))
        @foreach($dataAttributes as $key => $value)
            data-{{ $key }}="{{ $value }}"
        @endforeach
    @endif
>
    @if($icon && $iconPosition === 'left')
        <i class="{{ $icon }} me-2"></i>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }} ms-2"></i>
    @endif
</{{ $tag ?? 'button' }}>

<style>
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:active {
    transform: translateY(0);
}

/* Мобильная адаптация */
@media (max-width: 576px) {
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .btn-xl {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }
}
</style>
