@extends('layouts.app', [
    'header' => 'detail',
    'backUrl' => route('admin.articles.index'),
    'backText' => 'К списку статей',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Статьи', 'url' => route('admin.articles.index')],
        ['name' => 'Статья #1', 'url' => '#']
    ]
])

@section('content')
<div class="container-fluid admin-container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h4 class="h5 h4-md mb-2 mb-md-0">
                            <i class="fas fa-newspaper me-2 d-none d-md-inline"></i>
                            Статья #1
                        </h4>
                        <div class="text-light">
                            <small class="d-block d-md-inline">
                                <span class="fw-bold">ID:</span> 1
                            </small>
                            <small class="d-block d-md-inline ms-md-3">
                                <span class="fw-bold">Создано:</span> 15.01.2025 10:30
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="#" class="admin-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Основная информация -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    <i class="fas fa-info-circle me-2 d-none d-md-inline"></i>
                                    Основная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">
                                                Заголовок <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" 
                                                   name="title" 
                                                   value="{{ old('title', 'Пример заголовка статьи') }}"
                                                   required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">
                                                Статус <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" 
                                                    name="status"
                                                    required>
                                                <option value="">Выберите статус</option>
                                                <option value="draft" selected>Черновик</option>
                                                <option value="published">Опубликовано</option>
                                                <option value="archived">Архив</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">
                                        Содержание <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" 
                                              name="content" 
                                              rows="8"
                                              required>{{ old('content', 'Содержание статьи...') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEO информация -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    <i class="fas fa-search me-2 d-none d-md-inline"></i>
                                    SEO настройки
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta заголовок</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="meta_title" 
                                                   name="meta_title" 
                                                   value="{{ old('meta_title', 'SEO заголовок') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta описание</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="meta_description" 
                                                   name="meta_description" 
                                                   value="{{ old('meta_description', 'SEO описание') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex flex-column flex-md-row gap-2 action-buttons">
                                <button type="submit" class="btn btn-primary btn-sm btn-md">
                                    <i class="fas fa-save me-1 me-md-2"></i>Сохранить
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-md" onclick="resetForm()">
                                    <i class="fas fa-undo me-1 me-md-2"></i>Сбросить
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-md" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-1 me-md-2"></i>Удалить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        document.querySelector('form').reset();
    }
}

function confirmDelete() {
    if (confirm('Вы уверены, что хотите удалить эту статью?\n\nЭто действие нельзя отменить.')) {
        // Здесь будет логика удаления
        alert('Статья будет удалена');
    }
}
</script>
@endsection
