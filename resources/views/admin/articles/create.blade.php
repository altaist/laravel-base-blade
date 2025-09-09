@extends('layouts.app', [
    'header' => 'detail',
    'backUrl' => route('admin.articles.index'),
    'backText' => 'К списку статей',
    'title' => 'Создание статьи',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Статьи', 'url' => route('admin.articles.index')],
        ['name' => 'Создание', 'url' => '#']
    ]
])

@section('content')
<div class="container-fluid admin-container">

    <div class="row">
        <div class="col-12">
            <!-- Десктопная версия с карточкой -->
            <div class="card shadow-lg border-0 d-none d-md-block">
                <div class="card-body p-3">
                    <!-- Кнопки действий сверху -->
                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2 mb-3">
                        <button type="submit" form="articleCreateForm" class="btn btn-success">
                            <i class="fas fa-save d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Создать</span>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                            <i class="fas fa-undo d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Сбросить</span>
                        </button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Отмена</span>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.articles.store') }}" id="articleCreateForm" class="admin-form">
                        @csrf
                        
                        <!-- Основная информация статьи -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    Основная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label fw-bold text-muted">Заголовок статьи</label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="slug" class="form-label fw-bold text-muted">Slug</label>
                                            <input type="text" 
                                                   class="form-control @error('slug') is-invalid @enderror" 
                                                   id="slug" 
                                                   name="slug" 
                                                   value="{{ old('slug') }}"
                                                   required>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label fw-bold text-muted">Статус</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" 
                                                    name="status" 
                                                    required>
                                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Черновик</option>
                                                <option value="ready_to_publish" {{ old('status') == 'ready_to_publish' ? 'selected' : '' }}>Готова к публикации</option>
                                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Опубликована</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO информация -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    SEO информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="seo_title" class="form-label fw-bold text-muted">SEO заголовок</label>
                                            <input type="text" 
                                                   class="form-control @error('seo_title') is-invalid @enderror" 
                                                   id="seo_title" 
                                                   name="seo_title" 
                                                   value="{{ old('seo_title') }}">
                                            @error('seo_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="seo_description" class="form-label fw-bold text-muted">SEO описание</label>
                                            <textarea class="form-control @error('seo_description') is-invalid @enderror" 
                                                      id="seo_description" 
                                                      name="seo_description" 
                                                      rows="3">{{ old('seo_description') }}</textarea>
                                            @error('seo_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Содержимое статьи -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    Содержимое статьи
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="content" class="form-label fw-bold text-muted">Основное содержимое</label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" 
                                              name="content" 
                                              rows="10" 
                                              required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="rich_content" class="form-label fw-bold text-muted">Rich Content (HTML)</label>
                                    <textarea class="form-control @error('rich_content') is-invalid @enderror" 
                                              id="rich_content" 
                                              name="rich_content" 
                                              rows="10">{{ old('rich_content') }}</textarea>
                                    @error('rich_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Мобильная версия -->
            <div class="d-md-none">
                <form method="POST" action="{{ route('admin.articles.store') }}" class="admin-form">
                    @csrf
                    
                    <!-- Основная информация -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Основная информация</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name_mobile" class="form-label">Заголовок</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name_mobile" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="slug_mobile" class="form-label">Slug</label>
                                <input type="text" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug_mobile" 
                                       name="slug" 
                                       value="{{ old('slug') }}"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="status_mobile" class="form-label">Статус</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status_mobile" 
                                        name="status" 
                                        required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Черновик</option>
                                    <option value="ready_to_publish" {{ old('status') == 'ready_to_publish' ? 'selected' : '' }}>Готова к публикации</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Опубликована</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">SEO информация</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="seo_title_mobile" class="form-label">SEO заголовок</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_title_mobile" 
                                       name="seo_title" 
                                       value="{{ old('seo_title') }}">
                            </div>
                            <div class="mb-3">
                                <label for="seo_description_mobile" class="form-label">SEO описание</label>
                                <textarea class="form-control" 
                                          id="seo_description_mobile" 
                                          name="seo_description" 
                                          rows="3">{{ old('seo_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Содержимое -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Содержимое</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="content_mobile" class="form-label">Основное содержимое</label>
                                <textarea class="form-control" 
                                          id="content_mobile" 
                                          name="content" 
                                          rows="8" 
                                          required>{{ old('content') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="rich_content_mobile" class="form-label">Rich Content</label>
                                <textarea class="form-control" 
                                          id="rich_content_mobile" 
                                          name="rich_content" 
                                          rows="8">{{ old('rich_content') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Создать
                        </button>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        document.getElementById('articleCreateForm').reset();
    }
}
</script>
@endsection
