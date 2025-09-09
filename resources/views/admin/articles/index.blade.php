@extends('layouts.app', [
    'header' => 'admin',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Статьи', 'url' => route('admin.articles.index')]
    ]
])

@section('content')
<div class="container-fluid admin-container">
    <!-- Заголовок -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm btn-md">
                        <i class="fas fa-arrow-left me-1 me-md-2"></i>Назад
                    </a>
                    <a href="#" class="btn btn-primary btn-sm btn-md">
                        <i class="fas fa-plus me-1 me-md-2"></i>Добавить статью
                    </a>
                    <a href="{{ route('admin.articles.edit', 1) }}" class="btn btn-outline-primary btn-sm btn-md">
                        <i class="fas fa-edit me-1 me-md-2"></i>Пример редактирования
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Поиск и фильтры -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="#" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Поиск по заголовку, содержанию...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-md">
                                    <i class="fas fa-search me-1 me-md-2"></i>Найти
                                </button>
                                <a href="#" class="btn btn-outline-secondary btn-sm btn-md">
                                    <i class="fas fa-times me-1 me-md-2"></i>Очистить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Список статей -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h5 class="h6 h5-md mb-2 mb-md-0">
                            <i class="fas fa-list me-2 d-none d-md-inline"></i>
                            Список статей
                        </h5>
                        <span class="badge bg-primary fs-6">
                            Всего: 0
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="empty-state">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Нет статей</h5>
                        <p class="text-muted">Пока не создано ни одной статьи</p>
                        <a href="#" class="btn btn-primary btn-sm btn-md">
                            <i class="fas fa-plus me-1 me-md-2"></i>Создать первую статью
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
