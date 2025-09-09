@props([
    'formId' => 'adminForm',
    'saveText' => 'Сохранить',
    'cancelUrl' => '#',
    'showReset' => true,
    'showCancel' => true,
    'variant' => 'desktop' // desktop, mobile, both
])

@if($variant === 'desktop' || $variant === 'both')
    <!-- Десктопная версия кнопок -->
    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2 mb-3 d-none d-md-block">
        <button type="submit" form="{{ $formId }}" class="btn btn-success">
            <i class="fas fa-save d-md-inline d-none"></i>
            <span class="d-none d-md-inline ms-1">{{ $saveText }}</span>
        </button>
        
        @if($showReset)
            <button type="button" class="btn btn-outline-danger" onclick="resetForm('{{ $formId }}')">
                <i class="fas fa-undo d-md-inline d-none"></i>
                <span class="d-none d-md-inline ms-1">Сбросить</span>
            </button>
        @endif
        
        @if($showCancel)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary">
                <i class="fas fa-times d-md-inline d-none"></i>
                <span class="d-none d-md-inline ms-1">Отмена</span>
            </a>
        @endif
    </div>
@endif

@if($variant === 'mobile' || $variant === 'both')
    <!-- Мобильная версия кнопок -->
    <div class="d-flex gap-2 mb-3 d-block d-md-none">
        <button type="submit" form="{{ $formId }}" class="btn btn-success flex-fill">
            <i class="fas fa-save"></i>
        </button>
        
        @if($showReset)
            <button type="button" class="btn btn-outline-danger flex-fill" onclick="resetForm('{{ $formId }}')">
                <i class="fas fa-undo"></i>
            </button>
        @endif
        
        @if($showCancel)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary flex-fill">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </div>
@endif

@if($variant === 'bottom')
    <!-- Кнопки внизу формы (десктоп) -->
    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2 mt-4 d-none d-md-block">
        <button type="submit" form="{{ $formId }}" class="btn btn-success">
            <i class="fas fa-save d-md-inline d-none"></i>
            <span class="d-none d-md-inline ms-1">{{ $saveText }}</span>
        </button>
        
        @if($showReset)
            <button type="button" class="btn btn-outline-danger" onclick="resetForm('{{ $formId }}')">
                <i class="fas fa-undo d-md-inline d-none"></i>
                <span class="d-none d-md-inline ms-1">Сбросить</span>
            </button>
        @endif
        
        @if($showCancel)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary">
                <i class="fas fa-times d-md-inline d-none"></i>
                <span class="d-none d-md-inline ms-1">Отмена</span>
            </a>
        @endif
    </div>
@endif

@if($variant === 'mobile-bottom')
    <!-- Кнопки внизу формы (мобильная) -->
    <div class="d-flex gap-2 mt-3 mb-3 d-block d-md-none">
        <button type="submit" form="{{ $formId }}" class="btn btn-success flex-fill">
            <i class="fas fa-save"></i>
        </button>
        
        @if($showReset)
            <button type="button" class="btn btn-outline-danger flex-fill" onclick="resetForm('{{ $formId }}')">
                <i class="fas fa-undo"></i>
            </button>
        @endif
        
        @if($showCancel)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary flex-fill">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </div>
@endif

@if($variant === 'mobile-grid')
    <!-- Мобильная версия с grid layout -->
    <div class="d-grid gap-2 d-block d-md-none">
        <button type="submit" form="{{ $formId }}" class="btn btn-success">
            <i class="fas fa-save me-2"></i>{{ $saveText }}
        </button>
        
        @if($showCancel)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Отмена
            </a>
        @endif
    </div>
@endif
