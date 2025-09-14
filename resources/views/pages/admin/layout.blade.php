@extends('layouts.admin', [
    'header' => 'admin',
    'breadcrumbs' => $breadcrumbs ?? []
])

@section('page-content')
<div class="container-fluid admin-container">
    <!-- Заголовок -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    @if(isset($backUrl))
                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm btn-md">
                            <i class="fas fa-arrow-left me-1 me-md-2"></i>{{ $backText ?? 'Назад' }}
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm btn-md">
                            <i class="fas fa-arrow-left me-1 me-md-2"></i>Назад
                        </a>
                    @endif
                </div>
                @if(isset($headerActions))
                    <div class="mt-2 mt-md-0">
                        {{ $headerActions }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="row">
        <div class="col-12">
            {{ $slot }}
        </div>
    </div>
</div>

<!-- Форма для удаления -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(id, name, entityName = 'запись') {
    if (confirm(`Вы уверены, что хотите удалить ${entityName} "${name}"?\n\nЭто действие нельзя отменить.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/${entityName}/${id}`;
        form.submit();
    }
}

// Обработка кликов по строкам таблицы
document.addEventListener('DOMContentLoaded', function() {
    const clickableRows = document.querySelectorAll('.clickable-row');
    
    clickableRows.forEach(function(row) {
        row.addEventListener('click', function() {
            const href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
    });
});
</script>
@endsection
