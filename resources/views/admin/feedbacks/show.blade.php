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
                        <i class="fas fa-comment-dots me-3 text-success"></i>
                        Сообщение #{{ $feedback->id }}
                    </h1>
                    <p class="text-muted mb-0">Детали сообщения от пользователя</p>
                </div>
                <div>
                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к списку
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Детали сообщения -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h4 class="mb-2 mb-md-0">
                            <i class="fas fa-user me-2"></i>
                            {{ $feedback->json_data['name'] ?? 'Аноним' }}
                        </h4>
                        <div class="text-light">
                            <small class="d-block d-md-inline">
                                <span class="fw-bold">ID:</span> {{ $feedback->id }}
                            </small>
                            <small class="d-block d-md-inline ms-md-3">
                                <span class="fw-bold">Дата:</span> {{ $feedback->created_at->format('d.m.Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Информация об отправителе -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-user-circle me-2"></i>
                                Информация об отправителе
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Имя</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            {{ $feedback->json_data['name'] ?? 'Не указано' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Контакт</label>
                                        <div class="p-3 bg-light rounded">
                                            @if(isset($feedback->json_data['contact']) && $feedback->json_data['contact'])
                                                @if(filter_var($feedback->json_data['contact'], FILTER_VALIDATE_EMAIL))
                                                    <a href="mailto:{{ $feedback->json_data['contact'] }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                                        {{ $feedback->json_data['contact'] }}
                                                    </a>
                                                @else
                                                    <i class="fas fa-phone me-2 text-primary"></i>
                                                    {{ $feedback->json_data['contact'] }}
                                                @endif
                                            @else
                                                <i class="fas fa-question-circle me-2 text-muted"></i>
                                                <span class="text-muted">Не указан</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Сообщение -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-comment me-2"></i>
                                Сообщение
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="p-4 bg-light rounded">
                                <div class="text-dark" style="white-space: pre-wrap;">
                                    {{ $feedback->json_data['comment'] ?? 'Нет комментария' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительная информация -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Дополнительная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">ID записи</label>
                                        <div class="p-2 bg-light rounded">
                                            <code>#{{ $feedback->id }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Обновлено</label>
                                        <div class="p-2 bg-light rounded">
                                            {{ $feedback->updated_at->format('d.m.Y в H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mt-4">
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Назад к списку
                            </a>
                        </div>
                        
                        <div class="d-flex flex-column flex-md-row gap-2">
                            @if(isset($feedback->json_data['contact']) && $feedback->json_data['contact'])
                                <a href="mailto:{{ $feedback->json_data['contact'] }}" 
                                   class="btn btn-success btn-lg">
                                    <i class="fas fa-reply me-2"></i>Ответить
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection