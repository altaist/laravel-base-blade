@extends('layouts.app', [
    'header' => 'admin',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')]
    ]
])

@section('content')
<div class="container-fluid admin-container">
    <!-- Заголовок -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="text-muted">
                    <small>Добро пожаловать, {{ Auth::user()->name }}!</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100 stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $userStats['total'] }}</h4>
                            <p class="card-text">Всего пользователей</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $userStats['recent'] }}</h4>
                            <p class="card-text">Новых за неделю</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-plus fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $userStats['with_telegram'] }}</h4>
                            <p class="card-text">С Telegram</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fab fa-telegram fa-2x opacity-75"></i>
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
                            <h4 class="card-title">{{ $feedbackStats['total'] }}</h4>
                            <p class="card-text">Сообщений</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Основные разделы -->
    <div class="row">
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-lg border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="card-title mb-1">{{ $userStats['total'] }}</h3>
                            <p class="card-text mb-0 opacity-75">Всего пользователей</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75">+{{ $userStats['recent'] }} новых за неделю</small>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-comments fa-3x text-success mb-3"></i>
                    </div>
                    <h5 class="card-title">Обратная связь</h5>
                    <p class="card-text text-muted">
                        Просмотр сообщений и обращений от пользователей
                    </p>
                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-success btn-sm btn-md">
                        <i class="fas fa-arrow-right me-1 me-md-2"></i>
                        Перейти к сообщениям
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="h6 h5-md mb-0">
                        <i class="fas fa-info-circle me-2 d-none d-md-inline"></i>
                        Дополнительная информация
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Роли пользователей</h6>
                            <ul class="list-unstyled">
                                <li><span class="badge bg-danger me-2">Admin</span> {{ $userStats['admins'] }} администраторов</li>
                                <li><span class="badge bg-warning me-2">Manager</span> {{ $userStats['managers'] }} менеджеров</li>
                                <li><span class="badge bg-info me-2">User</span> {{ $userStats['users'] }} пользователей</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Активность</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-user-plus text-success me-2"></i> {{ $userStats['recent'] }} новых пользователей за неделю</li>
                                <li><i class="fas fa-comments text-info me-2"></i> {{ $feedbackStats['recent'] }} новых сообщений за неделю</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Быстрые действия</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-users me-1"></i> Все пользователи
                                </a>
                                <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-comments me-1"></i> Все сообщения
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
