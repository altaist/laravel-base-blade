@extends('layouts.admin', [
    'header' => 'admin',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Обратная связь', 'url' => route('admin.feedbacks.index')]
    ]
])

@section('page-content')
<div class="container-fluid admin-container">
    <!-- Заголовок -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm btn-md">
                        <i class="fas fa-arrow-left me-1 me-md-2"></i>Назад
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Список сообщений -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h5 class="h6 h5-md mb-2 mb-md-0">
                            <i class="fas fa-list me-2 d-none d-md-inline"></i>
                            Список сообщений
                        </h5>
                        <span class="badge bg-success fs-6">
                            Всего: {{ $feedbacks->total() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($feedbacks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Отправитель</th>
                                        <th>Контакт</th>
                                        <th>Сообщение</th>
                                        <th>Дата отправки</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr class="clickable-row" data-href="{{ route('admin.feedbacks.show', $feedback) }}" style="cursor: pointer;">
                                            <td class="fw-bold">#{{ $feedback->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($feedback->json_data['name'] ?? 'А', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $feedback->json_data['name'] ?? 'Аноним' }}</div>
                                                        <small class="text-muted">Отправитель</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($feedback->json_data['contact']) && $feedback->json_data['contact'])
                                                    @if(filter_var($feedback->json_data['contact'], FILTER_VALIDATE_EMAIL))
                                                        <a href="mailto:{{ $feedback->json_data['contact'] }}" class="text-decoration-none" onclick="event.stopPropagation();">
                                                            {{ $feedback->json_data['contact'] }}
                                                        </a>
                                                    @else
                                                        <span>{{ $feedback->json_data['contact'] }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Не указан</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $feedback->json_data['comment'] ?? 'Нет комментария' }}">
                                                    {{ $feedback->json_data['comment'] ?? 'Нет комментария' }}
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $feedback->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group action-buttons" role="group" onclick="event.stopPropagation();">
                                                    <a href="{{ route('admin.feedbacks.show', $feedback) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Просмотр">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Нет сообщений</h5>
                            <p class="text-muted">Пока никто не оставил обратную связь</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Пагинация -->
    @if($feedbacks->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $feedbacks->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Обработка кликов по строкам таблицы
document.addEventListener('DOMContentLoaded', function() {
    const clickableRows = document.querySelectorAll('.clickable-row');
    
    clickableRows.forEach(function(row) {
        row.addEventListener('click', function() {
            const href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
        
        // Эффект при наведении теперь обрабатывается CSS
    });
});
</script>
@endsection
