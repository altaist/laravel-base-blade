@props(['item', 'config'])

<div class="btn-group action-buttons" role="group" onclick="event.stopPropagation();">
    @if(in_array('view', $config['permissions'] ?? []))
        <a href="{{ route($config['routes']['show'], $item) }}" 
           class="btn btn-sm btn-outline-primary"
           title="Просмотр">
            <i class="fas fa-eye"></i>
        </a>
    @endif
    
    @if(in_array('update', $config['permissions'] ?? []))
        @if(isset($item->isAdmin) && $item->isAdmin())
            {{-- Не показываем кнопку редактирования для админов --}}
        @else
            <a href="{{ route($config['routes']['edit'], $item) }}" 
               class="btn btn-sm btn-outline-primary"
               title="Редактировать">
                <i class="fas fa-edit"></i>
            </a>
        @endif
    @endif
    
    @if(in_array('delete', $config['permissions'] ?? []))
        @if(isset($item->isAdmin) && $item->isAdmin() && $item->id === Auth::id())
            {{-- Не показываем кнопку удаления для текущего админа --}}
        @else
            <button type="button" 
                    class="btn btn-sm btn-outline-danger"
                    onclick="confirmDelete({{ $item->id }}, '{{ $item->name ?? $item->id }}', '{{ $config['entity_name'] ?? 'запись' }}')"
                    title="Удалить">
                <i class="fas fa-trash"></i>
            </button>
        @endif
    @endif
</div>
