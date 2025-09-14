@props(['title', 'subtitle' => null, 'icon' => 'fas fa-info-circle'])

<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h4 class="h5 h4-md mb-2 mb-md-0">
                <i class="{{ $icon }} me-2 d-none d-md-inline"></i>
                {{ $title }}
            </h4>
            @if($subtitle)
                <div class="text-light">
                    {{ $subtitle }}
                </div>
            @endif
        </div>
    </div>
    
    <div class="card-body p-4">
        {{ $slot }}
    </div>
</div>
