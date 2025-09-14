@props(['item', 'config' => []])

<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h4 class="h5 h4-md mb-2 mb-md-0">
                <i class="fas fa-info-circle me-2 d-none d-md-inline"></i>
                {{ $item->name ?? $item->id ?? 'Детали записи' }}
            </h4>
            <div class="text-light">
                <small class="d-block d-md-inline">
                    <span class="fw-bold">ID:</span> {{ $item->id }}
                </small>
                @if(isset($item->created_at))
                    <small class="d-block d-md-inline ms-md-3">
                        <span class="fw-bold">Создано:</span> {{ $item->created_at->format('d.m.Y H:i') }}
                    </small>
                @endif
            </div>
        </div>
    </div>
    
    <div class="card-body p-4">
        {{ $slot }}
        
        <!-- Кнопки действий -->
        <div class="d-flex flex-column gap-3 mt-4">
            <div class="d-flex flex-column flex-md-row gap-2 action-buttons">
                @if(in_array('update', $config['permissions'] ?? []))
                    @if(isset($item->isAdmin) && !$item->isAdmin())
                        <a href="{{ route($config['routes']['edit'], $item) }}" class="btn btn-primary btn-sm btn-md">
                            <i class="fas fa-edit me-1 me-md-2"></i>Редактировать
                        </a>
                    @endif
                @endif
                
                @if(in_array('delete', $config['permissions'] ?? []))
                    @if(isset($item->isAdmin) && $item->isAdmin() && $item->id === Auth::id())
                        {{-- Не показываем кнопку удаления для текущего админа --}}
                    @else
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm btn-md"
                                onclick="confirmDelete({{ $item->id }}, '{{ $item->name ?? $item->id }}', '{{ $config['entity_name'] ?? 'запись' }}')">
                            <i class="fas fa-trash me-1 me-md-2"></i>Удалить
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
