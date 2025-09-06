{{-- Форма обратной связи для лендинга --}}
@php
    $title = $title ?? 'Свяжитесь с нами';
    $subtitle = $subtitle ?? 'Оставьте заявку и мы свяжемся с вами';
    $action = $action ?? route('feedback.store');
    $method = $method ?? 'POST';
    $showTitle = $showTitle ?? true;
@endphp

<section class="py-5 bg-light">
    <div class="container">
        @if($showTitle)
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">{{ $title }}</h2>
                @if($subtitle)
                    <p class="lead text-muted">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="{{ $action }}" method="{{ $method }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold">Имя *</label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}"
                                               placeholder="Введите ваше имя"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact" class="form-label fw-bold">Контакт *</label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('contact') is-invalid @enderror" 
                                               id="contact" 
                                               name="contact" 
                                               value="{{ old('contact') }}"
                                               placeholder="Email или телефон"
                                               required>
                                        @error('contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label fw-bold">Сообщение *</label>
                                <textarea class="form-control form-control-lg @error('comment') is-invalid @enderror" 
                                          id="comment" 
                                          name="comment" 
                                          rows="5" 
                                          placeholder="Опишите ваш вопрос или предложение"
                                          required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Отправить заявку
                                </button>
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success mt-3" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.form-control-lg {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control-lg:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-lg {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: transform 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-2px);
}

.card {
    border-radius: 15px;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
    
    .form-control-lg {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 1rem;
    }
}
</style>
