@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <!-- Навигация -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left me-2"></i>Назад к списку
                </a>
                <div>
                    <h2 class="display-6 fw-bold text-dark mb-0">Сообщение #{{ $feedback->id }}</h2>
                </div>
            </div>

            <!-- Детали фидбека -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                {{ $feedback->json_data['name'] ?? 'Не указано' }}
                            </h5>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $feedback->created_at->format('d.m.Y в H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">Контактная информация</label>
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    {{ $feedback->json_data['contact'] ?? 'Не указано' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">Дата отправки</label>
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-calendar me-2 text-primary"></i>
                                    {{ $feedback->created_at->format('d.m.Y в H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Сообщение</label>
                        <div class="p-4 bg-light rounded">
                            <div class="text-dark">
                                {{ $feedback->json_data['comment'] ?? 'Нет комментария' }}
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительная информация -->
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

            <!-- Действия -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Назад к списку
                </a>
                
                <div>
                    <a href="mailto:{{ $feedback->json_data['contact'] ?? '' }}" 
                       class="btn btn-primary me-2"
                       @if(!$feedback->json_data['contact']) disabled @endif>
                        <i class="fas fa-reply me-2"></i>Ответить
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
