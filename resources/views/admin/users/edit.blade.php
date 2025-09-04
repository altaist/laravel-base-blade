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
                        <i class="fas fa-user-edit me-3 text-primary"></i>
                        Редактирование пользователя
                    </h1>
                    <p class="text-muted mb-0">Изменение данных пользователя и персоны</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад к списку
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
                        <h4 class="mb-2 mb-md-0">
                            <i class="fas fa-user me-2"></i>
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
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userEditForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Основная информация пользователя -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Основная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                Имя пользователя <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', $user->name) }}"
                                                   placeholder="Введите имя пользователя"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email', $user->email) }}"
                                                   placeholder="Введите email"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
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
                                </div>
                            </div>
                        </div>

                        <!-- Информация о персоне -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-id-card me-2"></i>
                                    Личная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <x-person-edit-form :personData="$personData" :user="$user" />
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Сохранить изменения
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Сбросить
                                </button>
                            </div>
                            
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Отмена
                                </a>
                                @if(!$user->isAdmin() || $user->id !== Auth::id())
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-lg"
                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-trash me-2"></i>Удалить пользователя
                                    </button>
                                @endif
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
