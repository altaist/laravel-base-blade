@extends('layouts.app', [
    'header' => 'detail',
    'backUrl' => route('admin.users.index'),
    'backText' => 'К списку пользователей',
    'title' => 'Просмотр',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Пользователи', 'url' => route('admin.users.index')],
        ['name' => $user->email, 'url' => '#']
    ],
    'editUrl' => !$user->isAdmin() ? route('admin.users.edit', $user) : null
])

@section('content')
<div class="container-fluid admin-container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h4 class="h5 h4-md mb-2 mb-md-0">
                            <i class="fas fa-user me-2 d-none d-md-inline"></i>
                            {{ $user->name }}
                        </h4>
                        <div class="text-light">
                            <small class="d-block d-md-inline">
                                <span class="fw-bold">ID:</span> {{ $user->id }}
                            </small>
                            <small class="d-block d-md-inline ms-md-3">
                                <span class="fw-bold">Email:</span> {{ $user->email }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Основная информация пользователя -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                <i class="fas fa-user-cog me-2 d-none d-md-inline"></i>
                                Основная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Имя пользователя</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Email</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-envelope me-2 text-primary"></i>
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Роль</label>
                                        <div class="p-3 bg-light rounded">
                                            @switch($user->role->value)
                                                @case('admin')
                                                    <span class="badge bg-danger me-2">Администратор</span>
                                                    @break
                                                @case('manager')
                                                    <span class="badge bg-warning me-2">Менеджер</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-info me-2">Пользователь</span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Telegram</label>
                                        <div class="p-3 bg-light rounded">
                                            @if($user->telegram_id)
                                                <span class="badge bg-success">
                                                    <i class="fab fa-telegram me-1"></i>
                                                    {{ $user->telegram_username ?? 'ID: ' . $user->telegram_id }}
                                                </span>
                                            @else
                                                <i class="fas fa-question-circle me-2 text-muted"></i>
                                                <span class="text-muted">Не привязан</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Дата регистрации</label>
                                        <div class="p-3 bg-light rounded">
                                            <i class="fas fa-calendar me-2 text-primary"></i>
                                            {{ $user->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Информация о персоне -->
                    @if($user->person)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    <i class="fas fa-id-card me-2 d-none d-md-inline"></i>
                                    Личная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted">Имя</label>
                                            <div class="p-3 bg-light rounded">
                                                <i class="fas fa-user me-2 text-primary"></i>
                                                {{ $user->person->first_name ?? 'Не указано' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted">Фамилия</label>
                                            <div class="p-3 bg-light rounded">
                                                <i class="fas fa-user me-2 text-primary"></i>
                                                {{ $user->person->last_name ?? 'Не указано' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted">Телефон</label>
                                            <div class="p-3 bg-light rounded">
                                                <i class="fas fa-phone me-2 text-primary"></i>
                                                {{ $user->person->phone ?? 'Не указан' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted">Дата рождения</label>
                                            <div class="p-3 bg-light rounded">
                                                <i class="fas fa-birthday-cake me-2 text-primary"></i>
                                                {{ $user->person->birth_date ? $user->person->birth_date->format('d.m.Y') : 'Не указана' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Дополнительная информация -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                <i class="fas fa-info-circle me-2 d-none d-md-inline"></i>
                                Дополнительная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">ID записи</label>
                                        <div class="p-2 bg-light rounded">
                                            <code>#{{ $user->id }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Обновлено</label>
                                        <div class="p-2 bg-light rounded">
                                            {{ $user->updated_at->format('d.m.Y в H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="d-flex flex-column gap-3 mt-4">
                        <div class="d-flex flex-column flex-md-row gap-2 action-buttons">
                            @if(!$user->isAdmin())
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm btn-md">
                                    <i class="fas fa-edit me-1 me-md-2"></i>Редактировать
                                </a>
                            @endif
                            @if(!$user->isAdmin() || $user->id !== Auth::id())
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm btn-md"
                                        onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                    <i class="fas fa-trash me-1 me-md-2"></i>Удалить
                                </button>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Назад к списку
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Форма для удаления -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(userId, userName) {
    if (confirm(`Вы уверены, что хотите удалить пользователя "${userName}"?\n\nЭто действие нельзя отменить.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/users/${userId}`;
        form.submit();
    }
}
</script>
@endsection
