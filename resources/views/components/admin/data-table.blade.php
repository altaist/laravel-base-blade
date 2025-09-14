@props(['items', 'config', 'search' => ''])

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h5 class="h6 h5-md mb-2 mb-md-0">
                <i class="fas fa-list me-2 d-none d-md-inline"></i>
                {{ $config['title'] ?? 'Список записей' }}
            </h5>
            <span class="badge bg-primary fs-6">
                Всего: {{ $items->total() }}
            </span>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            @foreach($config['columns'] as $field => $column)
                                <th class="{{ $column['sortable'] ?? false ? 'sortable' : '' }}">
                                    {{ $column['label'] }}
                                </th>
                            @endforeach
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="clickable-row" data-href="{{ route($config['routes']['show'], $item) }}" style="cursor: pointer;">
                                @foreach($config['columns'] as $field => $column)
                                    <td>
                                        @if($column['type'] === 'badge')
                                            @if($field === 'role')
                                                @switch($item->role->value)
                                                    @case('admin')
                                                        <span class="badge bg-danger">Администратор</span>
                                                        @break
                                                    @case('manager')
                                                        <span class="badge bg-warning">Менеджер</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-info">Пользователь</span>
                                                @endswitch
                                            @elseif($field === 'telegram')
                                                @if($item->telegram_id)
                                                    <span class="badge bg-success">
                                                        <i class="fab fa-telegram me-1"></i>
                                                        {{ $item->telegram_username ?? 'ID: ' . $item->telegram_id }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Не привязан</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">{{ $item->{$field} ?? '—' }}</span>
                                            @endif
                                        @elseif($column['type'] === 'custom')
                                            @if($field === 'sender')
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($item->json_data['name'] ?? 'А', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $item->json_data['name'] ?? 'Аноним' }}</div>
                                                        <small class="text-muted">Отправитель</small>
                                                    </div>
                                                </div>
                                            @elseif($field === 'contact')
                                                @if(isset($item->json_data['contact']) && $item->json_data['contact'])
                                                    @if(filter_var($item->json_data['contact'], FILTER_VALIDATE_EMAIL))
                                                        <a href="mailto:{{ $item->json_data['contact'] }}" class="text-decoration-none" onclick="event.stopPropagation();">
                                                            {{ $item->json_data['contact'] }}
                                                        </a>
                                                    @else
                                                        <span>{{ $item->json_data['contact'] }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Не указан</span>
                                                @endif
                                            @elseif($field === 'message')
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $item->json_data['comment'] ?? 'Нет комментария' }}">
                                                    {{ $item->json_data['comment'] ?? 'Нет комментария' }}
                                                </div>
                                            @endif
                                        @elseif($field === 'name')
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $item->name }}</div>
                                                    @if($item->person)
                                                        <small class="text-muted">
                                                            {{ $item->person->first_name }} {{ $item->person->last_name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($field === 'created_at')
                                            <small class="text-muted">
                                                {{ $item->created_at->format('d.m.Y H:i') }}
                                            </small>
                                        @else
                                            {{ $item->{$field} ?? '—' }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    <x-admin.action-buttons :item="$item" :config="$config" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state text-center p-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">
                    @if($search)
                        Записи не найдены
                    @else
                        Нет записей
                    @endif
                </h5>
                <p class="text-muted">
                    @if($search)
                        Попробуйте изменить поисковый запрос
                    @else
                        В системе пока нет записей
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Пагинация -->
@if($items->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $items->links() }}
            </div>
        </div>
    </div>
@endif
