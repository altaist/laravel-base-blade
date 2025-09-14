@extends('layouts.admin', [
    'header' => 'admin',
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

@section('page-content')
<div class="container-fluid admin-container">
    <div class="row">
        <div class="col-12">
            <!-- Кнопки действий -->
            <x-admin.form-buttons 
                formId="userEditForm" 
                saveText="Сохранить" 
                cancelUrl="{{ route('admin.users.show', $user) }}" 
                variant="desktop" />
            
            <!-- Мобильные кнопки -->
            <div class="d-block d-md-none mb-3">
                <x-admin.form-buttons 
                    formId="userEditForm" 
                    saveText="Сохранить" 
                    cancelUrl="{{ route('admin.users.show', $user) }}" 
                    variant="mobile" />
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userEditForm" class="admin-form">
                @csrf
                @method('PUT')
                
                <x-admin.user-form 
                    :user="$user" 
                    :personData="$personData" 
                    mode="edit" 
                    formId="userEditForm" />

            </form>
            
            <!-- Кнопки действий снизу -->
            <x-admin.action-buttons 
                formId="userEditForm" 
                saveText="Сохранить" 
                cancelUrl="{{ route('admin.users.show', $user) }}" 
                variant="bottom" />
            
            <!-- Мобильные кнопки снизу -->
            <div class="d-block d-md-none mt-3">
                <x-admin.action-buttons 
                    formId="userEditForm" 
                    saveText="Сохранить" 
                    cancelUrl="{{ route('admin.users.show', $user) }}" 
                    variant="mobile-bottom" />
            </div>
        </div>
    </div>
</div>

<!-- Форма для удаления -->
<form id="deleteForm" method="POST" action="{{ route('admin.users.destroy', 0) }}" style="display: none;">
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