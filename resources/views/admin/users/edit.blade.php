@extends('layouts.app', [
    'header' => 'detail',
    'backUrl' => route('admin.users.show', $user),
    'backText' => 'К просмотру пользователя',
    'title' => 'Редактор',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Пользователи', 'url' => route('admin.users.index')],
        ['name' => $user->email, 'url' => route('admin.users.show', $user)],
        ['name' => 'Редактор', 'url' => '#']
    ]
])

@section('content')
<div class="container-fluid admin-container">

    <div class="row">
        <div class="col-12">
            <!-- Десктопная версия с карточкой -->
            <div class="card shadow-lg border-0 d-none d-md-block">
                <div class="card-body p-3">
                    <!-- Кнопки действий сверху -->
                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2 mb-3">
                        <button type="submit" form="userEditForm" class="btn btn-success">
                            <i class="fas fa-save d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Сохранить</span>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                            <i class="fas fa-undo d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Сбросить</span>
                        </button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times d-md-inline d-none"></i>
                            <span class="d-none d-md-inline ms-1">Отмена</span>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userEditForm" class="admin-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Основная информация пользователя -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
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
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    Личная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <x-person-edit-form :personData="$personData" :user="$user" />
                            </div>
                        </div>

                        <!-- Адрес -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    Адрес
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="address_street" class="form-label">Улица</label>
                                            <input type="text" 
                                                   class="form-control @error('address.street') is-invalid @enderror" 
                                                   id="address_street" 
                                                   name="address[street]" 
                                                   value="{{ old('address.street', $personData['address']['street'] ?? '') }}"
                                                   placeholder="Введите название улицы"
                                                   autocomplete="address-line1">
                                            @error('address.street')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="address_house" class="form-label">Дом</label>
                                            <input type="text" 
                                                   class="form-control @error('address.house') is-invalid @enderror" 
                                                   id="address_house" 
                                                   name="address[house]" 
                                                   value="{{ old('address.house', $personData['address']['house'] ?? '') }}"
                                                   placeholder="Номер дома"
                                                   autocomplete="address-line2">
                                            @error('address.house')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="address_apartment" class="form-label">Квартира</label>
                                            <input type="text" 
                                                   class="form-control @error('address.apartment') is-invalid @enderror" 
                                                   id="address_apartment" 
                                                   name="address[apartment]" 
                                                   value="{{ old('address.apartment', $personData['address']['apartment'] ?? '') }}"
                                                   placeholder="Номер квартиры">
                                            @error('address.apartment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="address_city" class="form-label">Город</label>
                                            <input type="text" 
                                                   class="form-control @error('address.city') is-invalid @enderror" 
                                                   id="address_city" 
                                                   name="address[city]" 
                                                   value="{{ old('address.city', $personData['address']['city'] ?? '') }}"
                                                   placeholder="Введите город"
                                                   autocomplete="address-level2">
                                            @error('address.city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="address_postal_code" class="form-label">Почтовый индекс</label>
                                            <input type="text" 
                                                   class="form-control @error('address.postal_code') is-invalid @enderror" 
                                                   id="address_postal_code" 
                                                   name="address[postal_code]" 
                                                   value="{{ old('address.postal_code', $personData['address']['postal_code'] ?? '') }}"
                                                   placeholder="Введите почтовый индекс"
                                                   autocomplete="postal-code"
                                                   pattern="[0-9]{6}">
                                            @error('address.postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки действий снизу -->
                        <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2 mt-4">
                            <button type="submit" form="userEditForm" class="btn btn-success">
                                <i class="fas fa-save d-md-inline d-none"></i>
                                <span class="d-none d-md-inline ms-1">Сохранить</span>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                <i class="fas fa-undo d-md-inline d-none"></i>
                                <span class="d-none d-md-inline ms-1">Сбросить</span>
                            </button>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times d-md-inline d-none"></i>
                                <span class="d-none d-md-inline ms-1">Отмена</span>
                            </a>
                        </div>

                    </form>
                </div>
            </div>
            
            <!-- Мобильная версия без карточки -->
            <div class="d-block d-md-none">
                <!-- Кнопки действий сверху -->
                <div class="d-flex gap-2 mb-3">
                    <button type="submit" form="userEditForm" class="btn btn-success flex-fill">
                        <i class="fas fa-save"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger flex-fill" onclick="resetForm()">
                        <i class="fas fa-undo"></i>
                    </button>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-times"></i>
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userEditForm" class="admin-form">
                    @csrf
                    @method('PUT')
                    
                    <!-- Основная информация пользователя -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
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
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                Личная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <x-person-edit-form :personData="$personData" :user="$user" />
                        </div>
                    </div>

                    <!-- Адрес -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="h6 h5-md mb-0">
                                Адрес
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address_street" class="form-label">Улица</label>
                                        <input type="text" 
                                               class="form-control @error('address.street') is-invalid @enderror" 
                                               id="address_street" 
                                               name="address[street]" 
                                               value="{{ old('address.street', $personData['address']['street'] ?? '') }}"
                                               placeholder="Введите название улицы"
                                               autocomplete="address-line1">
                                        @error('address.street')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="address_house" class="form-label">Дом</label>
                                        <input type="text" 
                                               class="form-control @error('address.house') is-invalid @enderror" 
                                               id="address_house" 
                                               name="address[house]" 
                                               value="{{ old('address.house', $personData['address']['house'] ?? '') }}"
                                               placeholder="Номер дома"
                                               autocomplete="address-line2">
                                        @error('address.house')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="address_apartment" class="form-label">Квартира</label>
                                        <input type="text" 
                                               class="form-control @error('address.apartment') is-invalid @enderror" 
                                               id="address_apartment" 
                                               name="address[apartment]" 
                                               value="{{ old('address.apartment', $personData['address']['apartment'] ?? '') }}"
                                               placeholder="Номер квартиры">
                                        @error('address.apartment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address_city" class="form-label">Город</label>
                                        <input type="text" 
                                               class="form-control @error('address.city') is-invalid @enderror" 
                                               id="address_city" 
                                               name="address[city]" 
                                               value="{{ old('address.city', $personData['address']['city'] ?? '') }}"
                                               placeholder="Введите город"
                                               autocomplete="address-level2">
                                        @error('address.city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address_postal_code" class="form-label">Почтовый индекс</label>
                                        <input type="text" 
                                               class="form-control @error('address.postal_code') is-invalid @enderror" 
                                               id="address_postal_code" 
                                               name="address[postal_code]" 
                                               value="{{ old('address.postal_code', $personData['address']['postal_code'] ?? '') }}"
                                               placeholder="Введите почтовый индекс"
                                               autocomplete="postal-code"
                                               pattern="[0-9]{6}">
                                        @error('address.postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки действий снизу -->
                    <div class="d-flex gap-2 mt-3 mb-3">
                        <button type="submit" form="userEditForm" class="btn btn-success flex-fill">
                            <i class="fas fa-save"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger flex-fill" onclick="resetForm()">
                            <i class="fas fa-undo"></i>
                        </button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary flex-fill">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
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
