@extends('layouts.app', [
    'header' => 'profile',
    'showBackButton' => true,
    'backUrl' => route('dashboard'),
    'backText' => ''
])

@section('content')
<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-12">
            <!-- Заголовок страницы -->
            <x-layout.page-header title="Редактирование профиля">
                <x-slot:meta>
                    <div class="page-header__meta">
                        <span><i class="fas fa-user-edit"></i> Пользователь: {{ $user->name }} ({{ $user->email }})</span>
                    </div>
                </x-slot:meta>
            </x-layout.page-header>
            
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('person.update') }}" id="personEditForm">
                        @csrf
                        @method('PUT')
                        
                        <x-forms.person-edit-form :personData="$personData" :user="$user" />
                    </form>
                    
                    <!-- Кнопки действий -->
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mt-4">
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Назад к профилю
                            </a>
                        </div>
                        
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button type="submit" form="personEditForm" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Сбросить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Дополнительные формы для частичного обновления -->
<form method="POST" action="{{ route('person.update.address') }}" id="addressForm" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="address" id="addressData">
</form>

<form method="POST" action="{{ route('person.update.additional-info') }}" id="additionalInfoForm" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="additional_info" id="additionalInfoData">
</form>

<script>
function resetForm() {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        document.querySelector('form').reset();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Валидация формы
    const form = document.getElementById('personEditForm');
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
