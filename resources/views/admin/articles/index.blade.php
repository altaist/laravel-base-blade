@extends('layouts.app', [
    'header' => 'admin',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Статьи', 'url' => route('admin.articles.index')]
    ]
])

@section('content')
<div class="container-fluid admin-container">

    <!-- Поиск и фильтры -->
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <form method="GET" action="{{ route('admin.articles.index') }}">
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Поиск по заголовку, содержанию, slug..."
                           onkeypress="if(event.key==='Enter') this.form.submit();">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if($search)
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
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
                        <span class="badge bg-primary fs-6 d-none d-md-inline">
                            Всего: {{ $articles->total() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($articles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Заголовок</th>
                                        <th>Slug</th>
                                        <th>Статус</th>
                                        <th>Дата создания</th>
                                        <th>Дата обновления</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($articles as $article)
                                        <tr class="clickable-row" data-href="{{ route('admin.articles.show', $article) }}" style="cursor: pointer;">
                                            <td class="fw-bold">#{{ $article->id }}</td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $article->name }}</div>
                                                    @if($article->seo_title)
                                                        <small class="text-muted">
                                                            {{ $article->seo_title }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ $article->slug }}</code>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $article->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $article->updated_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group action-buttons" role="group" onclick="event.stopPropagation();">
                                                    <a href="{{ route('admin.articles.edit', $article) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-article-id="{{ $article->id }}"
                                                            data-article-title="{{ $article->name }}"
                                                            onclick="confirmDeleteArticle(this)"
                                                            title="Удалить">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                @if($search)
                                    Статьи не найдены
                                @else
                                    Нет статей
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if($search)
                                    Попробуйте изменить поисковый запрос
                                @else
                                    В системе пока нет созданных статей
                                @endif
                            </p>
                            @if(!$search)
                                <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm btn-md">
                                    <i class="fas fa-plus me-1 me-md-2"></i>Создать первую статью
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Пагинация -->
    @if($articles->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $articles->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- Статистика статей -->
    <!--div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $articleStats['total'] }}</h4>
                            <p class="card-text">Всего статей</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-newspaper fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $articleStats['recent'] }}</h4>
                            <p class="card-text">Новых за неделю</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-plus fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $articleStats['published'] }}</h4>
                            <p class="card-text">Опубликованных</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $articleStats['draft'] + $articleStats['ready'] }}</h4>
                            <p class="card-text">Черновиков и готовых</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-edit fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div-->
</div>

<!-- Форма для удаления -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script src="{{ asset('js/admin-common.js') }}"></script>
<script>
function confirmDeleteArticle(button) {
    AdminUtils.confirmDeleteArticle(button);
}
</script>
@endsection
