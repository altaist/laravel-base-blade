@extends('layouts.app', [
    'header' => 'admin',
    'backUrl' => route('admin.articles.index'),
    'backText' => 'К списку статей',
    'title' => 'Просмотр',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Статьи', 'url' => route('admin.articles.index')],
        ['name' => $article->name , 'url' => '#']
    ],
    'editUrl' => route('admin.articles.edit', $article)
])

@section('content')
<div class="container-fluid admin-container">
    <div class="row">
        <div class="col-12">
            <!-- Десктопная версия с карточкой -->
            <div class="card shadow-lg border-0 d-none d-md-block">
                <div class="card-body p-3">
                    <!-- Основная информация статьи -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                Основная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Заголовок статьи</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-newspaper me-2 text-primary"></i>
                                            {{ $article->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Slug</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-link me-2 text-primary"></i>
                                            <code>{{ $article->slug }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Статус</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-flag me-2 text-primary"></i>
                                            @switch($article->status->value)
                                                @case('published')
                                                    <span class="badge bg-success">Опубликована</span>
                                                    @break
                                                @case('draft')
                                                    <span class="badge bg-secondary">Черновик</span>
                                                    @break
                                                @case('ready_to_publish')
                                                    <span class="badge bg-warning">Готова к публикации</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-info">{{ $article->status->value }}</span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Дата создания</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                                            {{ $article->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Дата обновления</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-calendar-edit me-2 text-primary"></i>
                                            {{ $article->updated_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO информация -->
                    @if($article->seo_title || $article->seo_description)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                SEO информация
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($article->seo_title)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">SEO заголовок</label>
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    {{ $article->seo_title }}
                                </div>
                            </div>
                            @endif
                            
                            @if($article->seo_description)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">SEO описание</label>
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-align-left me-2 text-primary"></i>
                                    {{ $article->seo_description }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Содержимое статьи -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                Содержимое статьи
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Основное содержимое</label>
                                <div class="p-3 bg-light rounded">
                                    <div class="content-preview">
                                        {!! nl2br(e($article->content)) !!}
                                    </div>
                                </div>
                            </div>
                            
                            @if($article->rich_content)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Rich Content</label>
                                <div class="p-3 bg-light rounded">
                                    <div class="rich-content-preview">
                                        {!! $article->rich_content !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-primary">
                            <i class="fas fa-edit d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Редактировать</span>
                        </a>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Отмена</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Мобильная версия -->
            <div class="d-md-none">
                <!-- Основная информация -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Основная информация</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Заголовок</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-newspaper me-2 text-primary"></i>
                                {{ $article->name }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Slug</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-link me-2 text-primary"></i>
                                <code>{{ $article->slug }}</code>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Статус</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-flag me-2 text-primary"></i>
                                @switch($article->status->value)
                                    @case('published')
                                        <span class="badge bg-success">Опубликована</span>
                                        @break
                                    @case('draft')
                                        <span class="badge bg-secondary">Черновик</span>
                                        @break
                                    @case('ready_to_publish')
                                        <span class="badge bg-warning">Готова к публикации</span>
                                        @break
                                    @default
                                        <span class="badge bg-info">{{ $article->status->value }}</span>
                                @endswitch
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Дата создания</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-calendar-plus me-2 text-primary"></i>
                                {{ $article->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Дата обновления</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-calendar-edit me-2 text-primary"></i>
                                {{ $article->updated_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                @if($article->seo_title || $article->seo_description)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">SEO информация</h6>
                    </div>
                    <div class="card-body">
                        @if($article->seo_title)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">SEO заголовок</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-tag me-2 text-primary"></i>
                                {{ $article->seo_title }}
                            </div>
                        </div>
                        @endif
                        
                        @if($article->seo_description)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">SEO описание</label>
                            <div class="p-2 bg-light rounded">
                                <i class="fas fa-align-left me-2 text-primary"></i>
                                {{ $article->seo_description }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Содержимое -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Содержимое</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Основное содержимое</label>
                            <div class="p-2 bg-light rounded">
                                <div class="content-preview">
                                    {!! nl2br(e($article->content)) !!}
                                </div>
                            </div>
                        </div>
                        
                        @if($article->rich_content)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Rich Content</label>
                            <div class="p-2 bg-light rounded">
                                <div class="rich-content-preview">
                                    {!! $article->rich_content !!}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Кнопки действий для мобильной версии -->
                <div class="d-flex gap-2 mt-3 mb-3">
                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-primary flex-fill">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
