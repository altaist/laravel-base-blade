@extends('layouts.admin')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['name' => 'Админка', 'url' => route('admin.dashboard')]
        ];
    @endphp
@endsection

@section('page-content')



    <!-- Основные разделы -->
    <div class="row">
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <h2 class="card-title text-dark mb-2">{{ $userStats['total'] }}</h2>
                        <p class="text-muted mb-1">Всего пользователей</p>
                        <small class="text-success fw-semibold">+{{ $userStats['recent'] }} новых за неделю</small>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-users me-2"></i>
                        Пользователи
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-comments fa-2x text-success"></i>
                        </div>
                        <h2 class="card-title text-dark mb-2">{{ $feedbackStats['total'] }}</h2>
                        <p class="text-muted mb-1">Всего сообщений</p>
                        <small class="text-success fw-semibold">+{{ $feedbackStats['recent'] }} новых за неделю</small>
                    </div>
                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-comments me-2"></i>
                        Обратная связь
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-newspaper fa-2x text-info"></i>
                        </div>
                        <h2 class="card-title text-dark mb-2">{{ $articleStats['total'] ?? 0 }}</h2>
                        <p class="text-muted mb-1">Всего статей</p>
                        <small class="text-info fw-semibold">+{{ $articleStats['recent'] ?? 0 }} новых за неделю</small>
                    </div>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-info btn-lg px-4">
                        <i class="fas fa-newspaper me-2"></i>
                        Статьи
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
                                <li><i class="fas fa-newspaper text-warning me-2"></i> {{ $articleStats['recent'] }} новых статей за неделю</li>
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
                                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-newspaper me-1"></i> Все статьи
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
