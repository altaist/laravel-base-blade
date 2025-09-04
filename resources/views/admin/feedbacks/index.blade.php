@extends('layouts.app', [
    'header' => 'admin'
])

@section('content')
<div class="container-fluid mt-4">
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold text-dark mb-2">
                        <i class="fas fa-comments me-3 text-success"></i>
                        Обратная связь
                    </h1>
                    <p class="text-muted mb-0">Управление сообщениями от пользователей</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к панели
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
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
                                                <div class="btn-group" role="group" onclick="event.stopPropagation();">
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
                        <div class="text-center py-5">
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

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
}
</style>

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
        
        // Добавляем эффект при наведении
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>
@endsection
