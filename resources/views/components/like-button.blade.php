@props([
    'likeable' => null,
    'likeableType' => null,
    'likeableId' => null,
    'isLiked' => false,
    'likesCount' => 0,
    'size' => 'md',
    'showCount' => true,
    'class' => ''
])

@php
    // Определяем тип и ID сущности
    if ($likeable) {
        $likeableType = get_class($likeable);
        $likeableId = $likeable->id;
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

<div class="like-button-container {{ $class }}">
    @auth
        <button 
            type="button"
            class="like-button inline-flex items-center gap-2 {{ $sizeClass }} rounded-lg border transition-colors duration-200 {{ $isLiked ? 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}"
            data-likeable-type="{{ $likeableType }}"
            data-likeable-id="{{ $likeableId }}"
            data-is-liked="{{ $isLiked ? 'true' : 'false' }}"
        >
            <svg class="{{ $iconSize }} {{ $isLiked ? 'fill-current' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            
            @if($showCount)
                <span class="likes-count font-medium">{{ $likesCount }}</span>
            @endif
        </button>
    @else
        {{-- Для незарегистрированных пользователей показываем только счетчик --}}
        <div class="inline-flex items-center gap-2 {{ $sizeClass }} text-gray-500">
            <svg class="{{ $iconSize }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            
            @if($showCount)
                <span class="likes-count font-medium">{{ $likesCount }}</span>
            @endif
        </div>
    @endauth
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const likeableType = this.dataset.likeableType;
            const likeableId = this.dataset.likeableId;
            const isLiked = this.dataset.isLiked === 'true';
            
            // Показываем состояние загрузки
            this.disabled = true;
            const originalContent = this.innerHTML;
            this.innerHTML = '<svg class="animate-spin w-5 h-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            try {
                const response = await fetch('/api/likes/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': 'Bearer ' + (localStorage.getItem('token') || ''),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        likeable_type: likeableType,
                        likeable_id: likeableId
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Обновляем состояние кнопки
                    this.dataset.isLiked = data.is_liked ? 'true' : 'false';
                    
                    // Обновляем стили
                    if (data.is_liked) {
                        this.className = this.className.replace('bg-white border-gray-200 text-gray-600 hover:bg-gray-50', 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100');
                        this.querySelector('svg').classList.add('fill-current');
                    } else {
                        this.className = this.className.replace('bg-red-50 border-red-200 text-red-600 hover:bg-red-100', 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50');
                        this.querySelector('svg').classList.remove('fill-current');
                    }
                    
                    // Обновляем счетчик
                    const countElement = this.querySelector('.likes-count');
                    if (countElement) {
                        countElement.textContent = data.likes_count;
                    }
                } else {
                    console.error('Ошибка при обновлении лайка');
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
