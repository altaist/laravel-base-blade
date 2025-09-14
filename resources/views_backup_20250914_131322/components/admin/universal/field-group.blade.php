@props([
    'fields' => [],
    'columns' => 1,
    'class' => '',
    'gap' => 'mb-3'
])

@php
    $columnClass = match($columns) {
        2 => 'col-md-6',
        3 => 'col-md-4',
        4 => 'col-md-3',
        6 => 'col-md-2',
        default => 'col-12'
    };
@endphp

<div class="field-group {{ $class }}">
    @if($columns > 1)
        <div class="row">
            @foreach($fields as $field)
                <div class="{{ $columnClass }} {{ $gap }}">
                    <x-admin.universal.form-field :field="$field" />
                </div>
            @endforeach
        </div>
    @else
        @foreach($fields as $field)
            <x-admin.universal.form-field :field="$field" />
        @endforeach
    @endif
</div>
