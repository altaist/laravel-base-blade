@props([
    'favoritable' => null,
    'favoritableType' => null,
    'favoritableId' => null,
    'isFavorited' => false,
    'favoritesCount' => 0,
    'size' => 'md',
    'showCount' => true,
    'class' => ''
])

@php
    // Определяем тип и ID сущности
    if ($favoritable) {
        $favoritableType = get_class($favoritable);
        $favoritableId = $favoritable->id;
    }
    
    // Размеры кнопки
    $sizes = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base'
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    
    // Классы для иконки
    $iconSizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6'
    ];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

<div class="favorite-button-container {{ $class }}">
    @auth
        <button 
            type="button"
            class="favorite-button inline-flex items-center gap-2 {{ $sizeClass }} rounded-lg border transition-colors duration-200 {{ $isFavorited ? 'bg-yellow-50 border-yellow-200 text-yellow-600 hover:bg-yellow-100' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}"
            data-favoritable-type="{{ $favoritableType }}"
            data-favoritable-id="{{ $favoritableId }}"
            data-is-favorited="{{ $isFavorited ? 'true' : 'false' }}"
        >
            <svg class="{{ $iconSize }} {{ $isFavorited ? 'fill-current' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
            </svg>
            
            @if($showCount)
                <span class="favorites-count font-medium">{{ $favoritesCount }}</span>
            @endif
        </button>
    @else
        {{-- Для незарегистрированных пользователей показываем только счетчик --}}
        <div class="inline-flex items-center gap-2 {{ $sizeClass }} text-gray-500">
            <svg class="{{ $iconSize }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
            </svg>
            
            @if($showCount)
                <span class="favorites-count font-medium">{{ $favoritesCount }}</span>
            @endif
        </div>
    @endauth
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoriteButtons = document.querySelectorAll('.favorite-button');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const favoritableType = this.dataset.favoritableType;
            const favoritableId = this.dataset.favoritableId;
            const isFavorited = this.dataset.isFavorited === 'true';
            
            // Показываем состояние загрузки
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<svg class="animate-spin w-5 h-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            try {
                const response = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': 'Bearer ' + (localStorage.getItem('token') || ''),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        favoritable_type: favoritableType,
                        favoritable_id: favoritableId
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Обновляем состояние кнопки
                    this.dataset.isFavorited = data.is_favorited ? 'true' : 'false';
                    
                    // Обновляем стили
                    if (data.is_favorited) {
                        this.className = this.className.replace('bg-white border-gray-200 text-gray-600 hover:bg-gray-50', 'bg-yellow-50 border-yellow-200 text-yellow-600 hover:bg-yellow-100');
                        this.querySelector('svg').classList.add('fill-current');
                    } else {
                        this.className = this.className.replace('bg-yellow-50 border-yellow-200 text-yellow-600 hover:bg-yellow-100', 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50');
                        this.querySelector('svg').classList.remove('fill-current');
                    }
                    
                    // Обновляем счетчик
                    const countElement = this.querySelector('.favorites-count');
                    if (countElement) {
                        countElement.textContent = data.favorites_count;
                    }
                } else {
                    console.error('Ошибка при обновлении избранного');
                }
            } catch (error) {
                console.error('Ошибка:', error);
            } finally {
                // Восстанавливаем кнопку
                this.disabled = false;
                this.innerHTML = originalContent;
            }
        });
    });
});
</script>
@endpush
