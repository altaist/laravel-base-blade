@props(['personData', 'user'])

<div class="person-edit-form">
    <!-- Основная информация -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Основная информация</h5>
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
                               value="{{ old('first_name', $personData['first_name']) }}"
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
                               value="{{ old('last_name', $personData['last_name']) }}"
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
                               value="{{ old('middle_name', $personData['middle_name']) }}"
                               placeholder="Введите отчество"
                               autocomplete="additional-name">
                        @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $personData['email']) }}"
                               placeholder="Введите email"
                               autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $personData['phone']) }}"
                               placeholder="+7 (999) 123-45-67"
                               autocomplete="tel"
                               pattern="[\+]?[0-9\s\-\(\)]{10,20}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="region" class="form-label">Регион</label>
                        <input type="text" 
                               class="form-control @error('region') is-invalid @enderror" 
                               id="region" 
                               name="region" 
                               value="{{ old('region', $personData['region']) }}"
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
                            <option value="male" {{ old('gender', $personData['gender']) == 'male' ? 'selected' : '' }}>Мужской</option>
                            <option value="female" {{ old('gender', $personData['gender']) == 'female' ? 'selected' : '' }}>Женский</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Дата рождения</label>
                        <input type="date" 
                               class="form-control @error('birth_date') is-invalid @enderror" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $personData['birth_date']) }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="age" class="form-label">Возраст</label>
                        <input type="number" 
                               class="form-control @error('age') is-invalid @enderror" 
                               id="age" 
                               name="age" 
                               value="{{ old('age', $personData['age']) }}"
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
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Адрес</h5>
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

    <!-- Дополнительная информация (скрыто) -->
    <div class="card mb-4" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0">Дополнительная информация</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="additional_info" class="form-label">Дополнительная информация</label>
                <textarea class="form-control @error('additional_info') is-invalid @enderror" 
                          id="additional_info" 
                          name="additional_info" 
                          rows="4" 
                          placeholder="Введите дополнительную информацию">{{ old('additional_info', is_array($personData['additional_info']) ? json_encode($personData['additional_info'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $personData['additional_info']) }}</textarea>
                <div class="form-text">Можно ввести JSON формат или обычный текст</div>
                @error('additional_info')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Кнопки действий -->
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
        <a href="{{ route('profile') }}" class="btn btn-secondary order-2 order-md-1">
            <i class="fas fa-arrow-left me-2"></i>Назад к профилю
        </a>
        
        <div class="d-flex flex-column flex-md-row gap-2 order-1 order-md-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Сохранить изменения
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                <i class="fas fa-undo me-2"></i>Сбросить
            </button>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        document.querySelector('form').reset();
    }
}
</script>
