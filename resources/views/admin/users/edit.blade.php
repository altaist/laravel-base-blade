@extends('layouts.app')

@section('content')
<div class="container-fluid admin-container">
    <!-- Заголовок -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="h3 h1-md fw-bold text-dark mb-2">
                        <i class="fas fa-user-edit me-2 me-md-3 text-primary d-none d-md-inline"></i>
                        Редактирование пользователя
                    </h1>
                    <p class="text-muted mb-0 small d-none d-md-block">Изменение данных пользователя и персоны</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm btn-md">
                        <i class="fas fa-arrow-left me-1 me-md-2"></i>Назад
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userEditForm" class="admin-form">
                        @csrf
                        @method('PUT')
                        
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
                                            <label class="form-label">Имя пользователя</label>
                                            <div class="form-control-plaintext fw-bold">
                                                {{ $user->name }}
                                            </div>
                                            <small class="text-muted">Автоматически формируется из данных персоны</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <div class="form-control-plaintext">
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                    {{ $user->email }}
                                                </a>
                                            </div>
                                            <small class="text-muted">Неизменяемое поле</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">
                                                Роль <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('role') is-invalid @enderror" 
                                                    id="role" 
                                                    name="role"
                                                    required>
                                                <option value="">Выберите роль</option>
                                                <option value="admin" {{ old('role', $user->role->value) == 'admin' ? 'selected' : '' }}>
                                                    Администратор
                                                </option>
                                                <option value="manager" {{ old('role', $user->role->value) == 'manager' ? 'selected' : '' }}>
                                                    Менеджер
                                                </option>
                                                <option value="user" {{ old('role', $user->role->value) == 'user' ? 'selected' : '' }}>
                                                    Пользователь
                                                </option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Telegram</label>
                                            <div class="form-control-plaintext">
                                                @if($user->telegram_id)
                                                    <span class="badge bg-success">
                                                        <i class="fab fa-telegram me-1"></i>
                                                        {{ $user->telegram_username ?? 'ID: ' . $user->telegram_id }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Не привязан</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Дата регистрации</label>
                                            <div class="form-control-plaintext">
                                                {{ $user->created_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Информация о персоне -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    <i class="fas fa-id-card me-2 d-none d-md-inline"></i>
                                    Личная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <x-person-edit-form :personData="$personData" :user="$user" />
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex flex-column flex-md-row gap-2 action-buttons">
                                <button type="submit" class="btn btn-primary btn-sm btn-md">
                                    <i class="fas fa-save me-1 me-md-2"></i>Сохранить
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-md" onclick="resetForm()">
                                    <i class="fas fa-undo me-1 me-md-2"></i>Сбросить
                                </button>
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
                    </form>
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
function resetForm() {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        document.querySelector('form').reset();
    }
}

function confirmDelete(userId, userName) {
    if (confirm(`Вы уверены, что хотите удалить пользователя "${userName}"?\n\nЭто действие нельзя отменить.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/users/${userId}`;
        form.submit();
    }
}

// Валидация формы
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userEditForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Пожалуйста, заполните все обязательные поля');
        }
    });

    // Очистка ошибок при вводе
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>
@endsection
