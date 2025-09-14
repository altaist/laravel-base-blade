@props([
    'action' => '#',
    'method' => 'POST',
    'formId' => 'adminForm',
    'sections' => [],
    'cancelUrl' => '#',
    'saveText' => 'Сохранить',
    'showReset' => true,
    'showCancel' => true,
    'class' => 'admin-form',
    'enctype' => null
])

<form method="{{ $method }}" 
      action="{{ $action }}" 
      id="{{ $formId }}" 
      class="{{ $class }}"
      @if($enctype) enctype="{{ $enctype }}" @endif>
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    
    @if(!empty($sections))
        @foreach($sections as $section)
            <x-admin.universal.form-card 
                :title="$section['title']" 
                :fields="$section['fields'] ?? []"
                :class="$section['class'] ?? ''">
                @if(isset($section['slot']))
                    {!! $section['slot'] !!}
                @endif
            </x-admin.universal.form-card>
        @endforeach
    @else
        {{ $slot }}
    @endif
</form>

@if($showReset || $showCancel)
    <x-admin.action-buttons 
        :formId="$formId" 
        :saveText="$saveText" 
        :cancelUrl="$cancelUrl" 
        :showReset="$showReset"
        :showCancel="$showCancel"
        variant="both" />
@endif
