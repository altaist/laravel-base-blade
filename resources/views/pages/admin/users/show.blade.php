@extends('layouts.admin', [
    'header' => 'admin',
    'backUrl' => route('admin.users.index'),
    'backText' => 'К списку пользователей',
    'title' => 'Просмотр',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Пользователи', 'url' => route('admin.users.index')],
        ['name' => $user->email, 'url' => '#']
    ],
    'editUrl' => !$user->isAdmin() ? route('admin.users.edit', $user) : null
])

@section('page-content')
<div class="container-fluid admin-container">
    <div class="row">
        <div class="col-12">
            <x-admin.user-form 
                :user="$user" 
                :personData="[]" 
                mode="view" />

            <!-- Кнопки действий -->
            <div class="d-flex justify-content-end align-items-center mb-3">
                @if(!$user->isAdmin())
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        <span class="d-none d-md-inline">Редактировать</span>
                    </a>
                @endif
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