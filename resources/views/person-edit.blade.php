@extends('layouts.app', [
    'header' => 'profile',
    'showBackButton' => true,
    'backUrl' => route('profile'),
    'backText' => ''
])

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h4 class="mb-2 mb-md-0">
                            Редактирование профиля
                        </h4>
                        <small class="text-light text-break d-none d-md-block">
                            <span class="d-block d-md-inline">Пользователь:</span>
                            <span class="d-block d-md-inline">{{ $user->name }}</span>
                            <span class="d-block d-md-inline">({{ $user->email }})</span>
                        </small>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('person.update') }}" id="personEditForm">
                        @csrf
                        @method('PUT')
                        
                        <x-person-edit-form :personData="$personData" :user="$user" />
                    </form>
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
