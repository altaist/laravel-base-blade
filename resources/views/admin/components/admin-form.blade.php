@props(['action', 'method' => 'POST', 'config' => []])

<form method="{{ $method }}" action="{{ $action }}" id="adminForm" class="admin-form">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    
    {{ $slot }}
    
    <!-- Кнопки действий -->
    <div class="d-flex flex-column gap-3 mt-4">
        <div class="d-flex flex-column flex-md-row gap-2 action-buttons">
            <button type="submit" class="btn btn-primary btn-sm btn-md">
                <i class="fas fa-save me-1 me-md-2"></i>Сохранить
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-md" onclick="resetForm()">
                <i class="fas fa-undo me-1 me-md-2"></i>Сбросить
            </button>
        </div>
    </div>
</form>

<script>
function resetForm() {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        document.querySelector('form').reset();
    }
}

// Валидация формы
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('adminForm');
    if (form) {
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
    }
});
</script>
