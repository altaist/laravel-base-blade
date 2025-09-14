@extends('layouts.app', [
    'header' => 'admin',
    'backUrl' => route('admin.users.index'),
    'backText' => 'К списку пользователей',
    'title' => 'Создание пользователя',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Пользователи', 'url' => route('admin.users.index')],
        ['name' => 'Создание', 'url' => '#']
    ]
])

@section('content')
<div class="container-fluid admin-container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-body p-3">
                    <!-- Кнопки действий -->
                    <x-admin.form-buttons 
                        formId="userCreateForm" 
                        saveText="Создать" 
                        cancelUrl="{{ route('admin.users.index') }}" 
                        variant="desktop" />
                    
                    <!-- Мобильные кнопки -->
                    <div class="d-block d-md-none mb-3">
                        <x-admin.form-buttons 
                            formId="userCreateForm" 
                            saveText="Создать" 
                            cancelUrl="{{ route('admin.users.index') }}" 
                            variant="mobile" />
                    </div>

                    <form method="POST" action="{{ route('admin.users.store') }}" id="userCreateForm" class="admin-form">
                        @csrf
                        
                        <!-- Основная информация пользователя -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="h6 h5-md mb-0">
                                    Основная информация
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   placeholder="user@example.com"
                                                   required
                                                   autocomplete="email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
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
                                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                                    Администратор
                                                </option>
                                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>
                                                    Менеджер
                                                </option>
                                                <option value="user" {{ old('role', 'user') == 'user' ? 'selected' : '' }}>
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
                                            <label for="password" class="form-label">
                                                Пароль <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Введите пароль"
                                                   required
                                                   autocomplete="new-password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">
                                                Подтверждение пароля <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Подтвердите пароль"
                                                   required
                                                   autocomplete="new-password">
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">Имя</label>
                                            <input type="text" 
                                                   class="form-control @error('first_name') is-invalid @enderror" 
                                                   id="first_name" 
                                                   name="first_name" 
                                                   value="{{ old('first_name') }}"
                                                   placeholder="Введите имя"
                                                   autocomplete="given-name">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Фамилия</label>
                                            <input type="text" 
                                                   class="form-control @error('last_name') is-invalid @enderror" 
                                                   id="last_name" 
                                                   name="last_name" 
                                                   value="{{ old('last_name') }}"
                                                   placeholder="Введите фамилию"
                                                   autocomplete="family-name">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="middle_name" class="form-label">Отчество</label>
                                            <input type="text" 
                                                   class="form-control @error('middle_name') is-invalid @enderror" 
                                                   id="middle_name" 
                                                   name="middle_name" 
                                                   value="{{ old('middle_name') }}"
                                                   placeholder="Введите отчество"
                                                   autocomplete="additional-name">
                                            @error('middle_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Телефон</label>
                                            <input type="tel" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}"
                                                   placeholder="+7 (999) 123-45-67"
                                                   autocomplete="tel"
                                                   pattern="[\+]?[0-9\s\-\(\)]{10,20}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="region" class="form-label">Регион</label>
                                            <input type="text" 
                                                   class="form-control @error('region') is-invalid @enderror" 
                                                   id="region" 
                                                   name="region" 
                                                   value="{{ old('region') }}"
                                                   placeholder="Введите регион">
                                            @error('region')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">Пол</label>
                                            <select class="form-select @error('gender') is-invalid @enderror" 
                                                    id="gender" 
                                                    name="gender">
                                                <option value="">Выберите пол</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Мужской</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="birth_date" class="form-label">Дата рождения</label>
                                            <input type="date" 
                                                   class="form-control @error('birth_date') is-invalid @enderror" 
                                                   id="birth_date" 
                                                   name="birth_date" 
                                                   value="{{ old('birth_date') }}">
                                            @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="age" class="form-label">Возраст</label>
                                            <input type="number" 
                                                   class="form-control @error('age') is-invalid @enderror" 
                                                   id="age" 
                                                   name="age" 
                                                   value="{{ old('age') }}"
                                                   min="0" 
                                                   max="150"
                                                   step="1"
                                                   placeholder="Введите возраст"
                                                   autocomplete="bday-year">
                                            @error('age')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
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
                                                   value="{{ old('address.street') }}"
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
                                                   value="{{ old('address.house') }}"
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
                                                   value="{{ old('address.apartment') }}"
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
                                                   value="{{ old('address.city') }}"
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
                                                   value="{{ old('address.postal_code') }}"
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

                    </form>
                    
                    <!-- Кнопки действий снизу -->
                    <x-admin.action-buttons 
                        formId="userCreateForm" 
                        saveText="Создать" 
                        cancelUrl="{{ route('admin.users.index') }}" 
                        variant="bottom" />
                    
                    <!-- Мобильные кнопки снизу -->
                    <div class="d-block d-md-none mt-3">
                        <x-admin.action-buttons 
                            formId="userCreateForm" 
                            saveText="Создать" 
                            cancelUrl="{{ route('admin.users.index') }}" 
                            variant="mobile-bottom" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin-common.js') }}"></script>
@endsection