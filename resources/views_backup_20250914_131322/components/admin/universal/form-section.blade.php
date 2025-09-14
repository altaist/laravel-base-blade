@props([
    'title' => '',
    'fields' => [],
    'class' => '',
    'collapsible' => false,
    'collapsed' => false
])

@php
    $sectionId = 'section_' . uniqid();
@endphp

<div class="form-section {{ $class }}">
    @if($title)
        @if($collapsible)
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="h6 h5-md mb-0">
                        <button class="btn btn-link p-0 text-decoration-none" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#{{ $sectionId }}" 
                                aria-expanded="{{ $collapsed ? 'false' : 'true' }}" 
                                aria-controls="{{ $sectionId }}">
                            <i class="fas fa-chevron-{{ $collapsed ? 'right' : 'down' }} me-2"></i>
                            {{ $title }}
                        </button>
                    </h5>
                </div>
                <div class="collapse {{ $collapsed ? '' : 'show' }}" id="{{ $sectionId }}">
                    <div class="card-body">
                        @if(!empty($fields))
                            @foreach($fields as $field)
                                <x-admin.universal.form-field :field="$field" />
                            @endforeach
                        @else
                            {{ $slot }}
                        @endif
                    </div>
                </div>
            </div>
        @else
            <x-admin.universal.form-card :title="$title" :fields="$fields" :class="$class">
                {{ $slot }}
            </x-admin.universal.form-card>
        @endif
    @else
        <div class="form-fields {{ $class }}">
            @if(!empty($fields))
                @foreach($fields as $field)
                    <x-admin.universal.form-field :field="$field" />
                @endforeach
            @else
                {{ $slot }}
            @endif
        </div>
    @endif
</div>
