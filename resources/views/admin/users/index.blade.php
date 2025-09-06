@extends('layouts.app', [
    'header' => 'admin'
])

@section('content')
<style>
/* Адаптивные размеры кнопок */
.btn-sm.btn-md {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}
@media (min-width: 768px) {
    .btn-sm.btn-md {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
}

/* Адаптивные заголовки */
.h3.h1-md {
    font-size: 1.75rem;
}
@media (min-width: 768px) {
    .h3.h1-md {
        font-size: 2.5rem;
    }
}

.h6.h5-md {
    font-size: 1rem;
}
@media (min-width: 768px) {
    .h6.h5-md {
        font-size: 1.25rem;
    }
}

/* Мобильная таблица */
@media (max-width: 767px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    .table th, .table td {
        padding: 0.5rem 0.25rem;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
}
</style>

<div class="container-fluid mt-4">
    <!-- Заголовок -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h3 h1-md fw-bold text-dark mb-2">
                        <i class="fas fa-users me-2 me-md-3 text-primary d-none d-md-inline"></i>
                        Управление пользователями
                    </h1>
                    <p class="text-muted mb-0 small d-none d-md-block">Просмотр и редактирование пользователей системы</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm btn-md">
                        <i class="fas fa-arrow-left me-1 me-md-2"></i>Назад
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
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       value="{{ $search }}"
                                       placeholder="Поиск по имени, email, телефону...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-md">
                                    <i class="fas fa-search me-1 me-md-2"></i>Найти
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm btn-md">
                                        <i class="fas fa-times me-1 me-md-2"></i>Очистить
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Список пользователей -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h5 class="h6 h5-md mb-2 mb-md-0">
                            <i class="fas fa-list me-2 d-none d-md-inline"></i>
                            Список пользователей
                        </h5>
                        <span class="badge bg-primary fs-6">
                            Всего: {{ $users->total() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Пользователь</th>
                                        <th>Email</th>
                                        <th>Роль</th>
                                        <th>Telegram</th>
                                        <th>Дата регистрации</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="clickable-row" data-href="{{ route('admin.users.edit', $user) }}" style="cursor: pointer;">
                                            <td class="fw-bold">#{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $user->name }}</div>
                                                        @if($user->person)
                                                            <small class="text-muted">
                                                                {{ $user->person->first_name }} {{ $user->person->last_name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none" onclick="event.stopPropagation();">
                                                    {{ $user->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @switch($user->role->value)
                                                    @case('admin')
                                                        <span class="badge bg-danger">Администратор</span>
                                                        @break
                                                    @case('manager')
                                                        <span class="badge bg-warning">Менеджер</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-info">Пользователь</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($user->telegram_id)
                                                    <span class="badge bg-success">
                                                        <i class="fab fa-telegram me-1"></i>
                                                        {{ $user->telegram_username ?? 'ID: ' . $user->telegram_id }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Не привязан</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" onclick="event.stopPropagation();">
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$user->isAdmin() || $user->id !== Auth::id())
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                                                title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                @if($search)
                                    Пользователи не найдены
                                @else
                                    Нет пользователей
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if($search)
                                    Попробуйте изменить поисковый запрос
                                @else
                                    В системе пока нет зарегистрированных пользователей
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Пагинация -->
    @if($users->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Форма для удаления -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
}
</style>

<script>
function confirmDelete(userId, userName) {
    if (confirm(`Вы уверены, что хотите удалить пользователя "${userName}"?\n\nЭто действие нельзя отменить.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/users/${userId}`;
        form.submit();
    }
}

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
