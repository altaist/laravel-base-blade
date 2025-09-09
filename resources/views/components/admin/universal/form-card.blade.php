@props(['title', 'fields' => [], 'class' => ''])

<div class="card mb-3 {{ $class }}">
    <div class="card-header bg-light">
        <h5 class="h6 h5-md mb-0">{{ $title }}</h5>
    </div>
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
