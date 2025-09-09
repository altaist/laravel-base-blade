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
                    <x-admin.action-buttons 
                        formId="userEditForm" 
                        saveText="Сохранить" 
                        cancelUrl="{{ route('admin.users.show', $user) }}" 
                        variant="desktop" />

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
                        <x-admin.action-buttons 
                            formId="userEditForm" 
                            saveText="Сохранить" 
                            cancelUrl="{{ route('admin.users.show', $user) }}" 
                            variant="bottom" />

                    </form>
                </div>
            </div>
            
            <!-- Мобильная версия без карточки -->
            <div class="d-block d-md-none">
                <!-- Кнопки действий сверху -->
                <x-admin.action-buttons 
                    formId="userEditForm" 
                    saveText="Сохранить" 
                    cancelUrl="{{ route('admin.users.show', $user) }}" 
                    variant="mobile" />

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
                    <x-admin.action-buttons 
                        formId="userEditForm" 
                        saveText="Сохранить" 
                        cancelUrl="{{ route('admin.users.show', $user) }}" 
                        variant="mobile-bottom" />
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

<script src="{{ asset('js/admin-common.js') }}"></script>
<script>
function confirmDelete(userId, userName) {
    AdminUtils.confirmDelete(userId, userName, 'пользователя');
}
</script>
@endsection
