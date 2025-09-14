<div class="w-100">
    <form action="{{ route('feedback.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Имя</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
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
                    <label for="contact" class="form-label">Контакт</label>
                    <input type="text" 
                           class="form-control @error('contact') is-invalid @enderror" 
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
            <label for="comment" class="form-label">Комментарий</label>
            <textarea class="form-control @error('comment') is-invalid @enderror" 
                      id="comment" 
                      name="comment" 
                      rows="4" 
                      placeholder="Опишите ваш вопрос или предложение"
                      required>{{ old('comment') }}</textarea>
            @error('comment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Отправить
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
    </form>
</div>
