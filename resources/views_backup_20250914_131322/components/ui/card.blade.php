{{-- Переиспользуемый компонент карточки --}}
@php
    $variant = $variant ?? 'default';
    $shadow = $shadow ?? 'sm';
    $border = $border ?? true;
    $hover = $hover ?? false;
    $class = $class ?? '';
    $bodyClass = $bodyClass ?? '';
@endphp

@php
    $cardClass = 'card';
    
    // Тень
    if($shadow) {
        $cardClass .= ' shadow-' . $shadow;
    }
    
    // Граница
    if(!$border) {
        $cardClass .= ' border-0';
    }
    
    // Дополнительные классы
    if($class) {
        $cardClass .= ' ' . $class;
    }
    
    $bodyClasses = 'card-body';
    if($bodyClass) {
        $bodyClasses .= ' ' . $bodyClass;
    }
@endphp

<div class="{{ $cardClass }}" @if($hover) data-hover="true" @endif>
    @if(isset($header))
        <div class="card-header">
            {{ $header }}
        </div>
    @endif
    
    @if(isset($image))
        <img src="{{ $image }}" class="card-img-top" alt="{{ $imageAlt ?? 'Изображение' }}">
    @endif
    
    <div class="{{ $bodyClasses }}">
        @if(isset($title))
            <h5 class="card-title">{{ $title }}</h5>
        @endif
        
        @if(isset($subtitle))
            <h6 class="card-subtitle mb-2 text-muted">{{ $subtitle }}</h6>
        @endif
        
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>

<style>
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.card[data-hover="true"]:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.card-img-top {
    border-radius: 12px 12px 0 0;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .card {
        margin-bottom: 1rem;
    }
}
</style>
