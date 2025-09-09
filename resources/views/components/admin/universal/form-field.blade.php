@props(['field'])

@php
    $fieldName = $field['name'] ?? '';
    $fieldLabel = $field['label'] ?? '';
    $fieldType = $field['type'] ?? 'text';
    $fieldValue = $field['value'] ?? '';
    $fieldRequired = $field['required'] ?? false;
    $fieldPlaceholder = $field['placeholder'] ?? '';
    $fieldOptions = $field['options'] ?? [];
    $fieldRows = $field['rows'] ?? 3;
    $fieldClass = $field['class'] ?? '';
    $fieldAttributes = $field['attributes'] ?? [];
    $fieldHelp = $field['help'] ?? '';
    $fieldReadonly = $field['readonly'] ?? false;
    $fieldDisabled = $field['disabled'] ?? false;
    
    // Формируем атрибуты
    $attributes = '';
    foreach ($fieldAttributes as $attr => $value) {
        $attributes .= " {$attr}=\"{$value}\"";
    }
@endphp

<div class="mb-3 {{ $fieldClass }}">
    <label for="{{ $fieldName }}" class="form-label">
        {{ $fieldLabel }}
        @if($fieldRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    @if($fieldType === 'select')
        <select class="form-select @error($fieldName) is-invalid @enderror" 
                id="{{ $fieldName }}" 
                name="{{ $fieldName }}" 
                {{ $fieldRequired ? 'required' : '' }}
                {{ $fieldReadonly ? 'readonly' : '' }}
                {{ $fieldDisabled ? 'disabled' : '' }}
                {!! $attributes !!}>
            @if(isset($field['empty_option']))
                <option value="">{{ $field['empty_option'] }}</option>
            @endif
            @foreach($fieldOptions as $value => $label)
                <option value="{{ $value }}" {{ old($fieldName, $fieldValue) == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        
    @elseif($fieldType === 'textarea')
        <textarea class="form-control @error($fieldName) is-invalid @enderror" 
                  id="{{ $fieldName }}" 
                  name="{{ $fieldName }}" 
                  rows="{{ $fieldRows }}"
                  placeholder="{{ $fieldPlaceholder }}"
                  {{ $fieldRequired ? 'required' : '' }}
                  {{ $fieldReadonly ? 'readonly' : '' }}
                  {{ $fieldDisabled ? 'disabled' : '' }}
                  {!! $attributes !!}>{{ old($fieldName, $fieldValue) }}</textarea>
                  
    @elseif($fieldType === 'checkbox')
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input @error($fieldName) is-invalid @enderror" 
                   id="{{ $fieldName }}" 
                   name="{{ $fieldName }}" 
                   value="1"
                   {{ old($fieldName, $fieldValue) ? 'checked' : '' }}
                   {{ $fieldRequired ? 'required' : '' }}
                   {{ $fieldReadonly ? 'readonly' : '' }}
                   {{ $fieldDisabled ? 'disabled' : '' }}
                   {!! $attributes !!}>
            <label class="form-check-label" for="{{ $fieldName }}">
                {{ $fieldLabel }}
            </label>
        </div>
        
    @elseif($fieldType === 'radio')
        <div class="form-group">
            @foreach($fieldOptions as $value => $label)
                <div class="form-check">
                    <input type="radio" 
                           class="form-check-input @error($fieldName) is-invalid @enderror" 
                           id="{{ $fieldName }}_{{ $value }}" 
                           name="{{ $fieldName }}" 
                           value="{{ $value }}"
                           {{ old($fieldName, $fieldValue) == $value ? 'checked' : '' }}
                           {{ $fieldRequired ? 'required' : '' }}
                           {{ $fieldReadonly ? 'readonly' : '' }}
                           {{ $fieldDisabled ? 'disabled' : '' }}
                           {!! $attributes !!}>
                    <label class="form-check-label" for="{{ $fieldName }}_{{ $value }}">
                        {{ $label }}
                    </label>
                </div>
            @endforeach
        </div>
        
    @elseif($fieldType === 'file')
        <input type="file" 
               class="form-control @error($fieldName) is-invalid @enderror" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}"
               {{ $fieldRequired ? 'required' : '' }}
               {{ $fieldReadonly ? 'readonly' : '' }}
               {{ $fieldDisabled ? 'disabled' : '' }}
               {!! $attributes !!}>
               
    @elseif($fieldType === 'hidden')
        <input type="hidden" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}" 
               value="{{ old($fieldName, $fieldValue) }}"
               {!! $attributes !!}>
               
    @elseif($fieldType === 'static')
        <div class="form-control-plaintext">
            {{ $fieldValue }}
        </div>
        
    @else
        {{-- Обычное текстовое поле или другие типы --}}
        <input type="{{ $fieldType }}" 
               class="form-control @error($fieldName) is-invalid @enderror" 
               id="{{ $fieldName }}" 
               name="{{ $fieldName }}" 
               value="{{ old($fieldName, $fieldValue) }}"
               placeholder="{{ $fieldPlaceholder }}"
               {{ $fieldRequired ? 'required' : '' }}
               {{ $fieldReadonly ? 'readonly' : '' }}
               {{ $fieldDisabled ? 'disabled' : '' }}
               {!! $attributes !!}>
    @endif
    
    @if($fieldHelp)
        <div class="form-text">{{ $fieldHelp }}</div>
    @endif
    
    @error($fieldName)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
