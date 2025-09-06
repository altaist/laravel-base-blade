{{-- Hero секция --}}
@php
    $title = $title ?? 'Добро пожаловать';
    $subtitle = $subtitle ?? 'Описание вашего продукта или услуги';
    $buttonText = $buttonText ?? 'Узнать больше';
    $buttonUrl = $buttonUrl ?? '#';
    $backgroundImage = $backgroundImage ?? null;
    $showButton = $showButton ?? true;
@endphp

<section class="hero-section py-5 {{ $backgroundImage ? 'hero-with-bg' : 'bg-primary' }}" 
         @if($backgroundImage) style="background-image: url('{{ $backgroundImage }}');" @endif>
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <div class="hero-content text-white">
                    <h1 class="display-4 fw-bold mb-4">{{ $title }}</h1>
                    <p class="lead mb-4">{{ $subtitle }}</p>
                    @if($showButton)
                        <a href="{{ $buttonUrl }}" class="btn btn-light btn-lg">
                            {{ $buttonText }}
                        </a>
                    @endif
                </div>
            </div>
            @if(isset($heroImage))
                <div class="col-lg-6">
                    <div class="hero-image text-center">
                        <img src="{{ $heroImage }}" alt="Hero Image" class="img-fluid rounded shadow">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-with-bg {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
}

.hero-with-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.min-vh-50 {
    min-height: 50vh;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .hero-content .lead {
        font-size: 1.1rem;
    }
    
    .min-vh-50 {
        min-height: 40vh;
    }
}

@media (max-width: 576px) {
    .hero-content h1 {
        font-size: 2rem;
    }
    
    .hero-content .lead {
        font-size: 1rem;
    }
}
</style>
