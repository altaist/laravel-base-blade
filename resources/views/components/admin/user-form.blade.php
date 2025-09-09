@props([
    'user',
    'personData' => [],
    'mode' => 'edit', // 'edit' или 'view'
    'formId' => 'userForm',
    'formAction' => '',
    'formMethod' => 'POST'
])

<div class="user-form">
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
                        <label for="name" class="form-label">
                            Имя пользователя <span class="text-danger">*</span>
                        </label>
                        @if($mode === 'edit')
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
                        @else
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->name }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="form-control-plaintext bg-light p-2 rounded">
                            {{ $user->email }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            Роль <span class="text-danger">*</span>
                        </label>
                        @if($mode === 'edit')
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
                        @else
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                @switch($user->role->value)
                                    @case('admin')
                                        <span class="badge bg-danger">Администратор</span>
                                        @break
                                    @case('manager')
                                        <span class="badge bg-warning">Менеджер</span>
                                        @break
                                    @case('user')
                                        <span class="badge bg-info">Пользователь</span>
                                        @break
                                @endswitch
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Telegram</label>
                        <div class="form-control-plaintext bg-light p-2 rounded">
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
                        <div class="form-control-plaintext bg-light p-2 rounded">
                            {{ $user->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Личная информация -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="h6 h5-md mb-0">
                Личная информация
            </h5>
        </div>
        <div class="card-body">
            @if($mode === 'edit')
                <x-person-edit-form :personData="$personData" :user="$user" />
            @else
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Имя</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->first_name ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Фамилия</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->last_name ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Отчество</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->middle_name ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email персоны</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->email ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Телефон</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->phone ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Регион</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->region ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Пол</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                @if($user->person->gender)
                                    {{ $user->person->gender === 'male' ? 'Мужской' : 'Женский' }}
                                @else
                                    Не указано
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Дата рождения</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->birth_date ? $user->person->birth_date->format('d.m.Y') : 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Возраст</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->age ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
            @if($mode === 'edit')
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
            @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Улица</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->address['street'] ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Дом</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->address['house'] ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Квартира</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->address['apartment'] ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Город</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->address['city'] ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Почтовый индекс</label>
                            <div class="form-control-plaintext bg-light p-2 rounded">
                                {{ $user->person->address['postal_code'] ?? 'Не указано' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
