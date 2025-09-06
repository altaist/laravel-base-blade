{{-- Call-to-Action секция --}}
@php
    $title = $title ?? 'Готовы начать?';
    $subtitle = $subtitle ?? 'Присоединяйтесь к нам прямо сейчас';
    $buttonText = $buttonText ?? 'Начать';
    $buttonUrl = $buttonUrl ?? '#';
    $buttonClass = $buttonClass ?? 'btn-primary';
    $backgroundClass = $backgroundClass ?? 'bg-primary';
    $textClass = $textClass ?? 'text-white';
@endphp

<section class="cta-section py-5 {{ $backgroundClass }}">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="cta-content {{ $textClass }}">
                    <h2 class="display-5 fw-bold mb-3">{{ $title }}</h2>
                    @if($subtitle)
                        <p class="lead mb-4">{{ $subtitle }}</p>
                    @endif
                    <a href="{{ $buttonUrl }}" class="btn {{ $buttonClass }} btn-lg">
                        {{ $buttonText }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.cta-section {
    position: relative;
}

.cta-content {
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .cta-content h2 {
        font-size: 2.5rem;
    }
    
    .cta-content .lead {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .cta-content h2 {
        font-size: 2rem;
    }
    
    .cta-content .lead {
        font-size: 1rem;
    }
}
</style>
