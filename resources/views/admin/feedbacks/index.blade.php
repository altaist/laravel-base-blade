@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="display-6 fw-bold text-dark mb-2">Обратная связь</h2>
                    <p class="text-muted">Управление сообщениями от пользователей</p>
                </div>
                <div class="text-muted">
                    <small>Всего сообщений: {{ $feedbacks->total() }}</small>
                </div>
            </div>

            <!-- Список фидбеков -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @if($feedbacks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Имя</th>
                                        <th>Контакт</th>
                                        <th>Комментарий</th>
                                        <th>Дата</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr>
                                            <td class="fw-bold">#{{ $feedback->id }}</td>
                                            <td>{{ $feedback->json_data['name'] ?? 'Не указано' }}</td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($feedback->json_data['contact'] ?? 'Не указано', 30) }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($feedback->json_data['comment'] ?? 'Нет комментария', 50) }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $feedback->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.feedbacks.show', $feedback) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Просмотр
                                                </a>
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

            <!-- Пагинация -->
            @if($feedbacks->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
