@extends('layouts.app', [
    'header' => 'admin',
    'breadcrumbs' => [
        ['name' => 'Админка', 'url' => route('admin.dashboard')],
        ['name' => 'Пользователи', 'url' => route('admin.users.index')]
    ]
])

@section('content')
<div class="container-fluid admin-container">

    <!-- Поиск и фильтры -->
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Поиск по имени, email, телефону..."
                           onkeypress="if(event.key==='Enter') this.form.submit();">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if($search)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Список пользователей -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h5 class="h6 h5-md mb-2 mb-md-0">
                            <i class="fas fa-list me-2 d-none d-md-inline"></i>
                            Список пользователей
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6 d-none d-md-inline">
                                Всего: {{ $users->total() }}
                            </span>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                <span class="d-none d-md-inline">Создать пользователя</span>
                                <span class="d-md-none">Создать</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Пользователь</th>
                                        <th>Email</th>
                                        <th>Роль</th>
                                        <th>Telegram</th>
                                        <th>Дата регистрации</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="clickable-row" data-href="{{ route('admin.users.show', $user) }}" style="cursor: pointer;">
                                            <td class="fw-bold">#{{ $user->id }}</td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    @if($user->person)
                                                        <small class="text-muted">
                                                            {{ $user->person->first_name }} {{ $user->person->last_name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{ $user->email }}
                                            </td>
                                            <td>
                                                @switch($user->role->value)
                                                    @case('admin')
                                                        <span class="badge bg-danger">Администратор</span>
                                                        @break
                                                    @case('manager')
                                                        <span class="badge bg-warning">Менеджер</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-info">Пользователь</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($user->telegram_id)
                                                    <span class="badge bg-success">
                                                        <i class="fab fa-telegram me-1"></i>
                                                        {{ $user->telegram_username ?? 'ID: ' . $user->telegram_id }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Не привязан</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group action-buttons" role="group" onclick="event.stopPropagation();">
                                                    @if(!$user->isAdmin())
                                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Редактировать">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(!$user->isAdmin() || $user->id !== Auth::id())
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                                                title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                @if($search)
                                    Пользователи не найдены
                                @else
                                    Нет пользователей
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if($search)
                                    Попробуйте изменить поисковый запрос
                                @else
                                    В системе пока нет зарегистрированных пользователей
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Пагинация -->
    @if($users->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    @endif

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
