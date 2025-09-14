<div class="detail-header">
    <div class="container-fluid">
        <a href="{{ $backUrl ?? '#' }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        
        <h1 class="detail-title">
            {{ $title ?? 'Редактирование' }}
        </h1>
        
        @if(isset($editUrl) && $editUrl)
            <a href="{{ $editUrl }}" class="edit-btn" title="Редактировать">
                <i class="fas fa-edit"></i>
            </a>
        @endif
    </div>
</div>
