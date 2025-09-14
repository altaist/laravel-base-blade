@props(['status', 'type' => 'default'])

@php
    $badgeClasses = [
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'pending' => 'bg-warning',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'draft' => 'bg-secondary',
        'published' => 'bg-success',
        'admin' => 'bg-danger',
        'manager' => 'bg-warning',
        'user' => 'bg-info',
        'default' => 'bg-primary'
    ];
    
    $badgeClass = $badgeClasses[$status] ?? $badgeClasses['default'];
@endphp

<span class="badge {{ $badgeClass }}">
    {{ $slot }}
</span>
