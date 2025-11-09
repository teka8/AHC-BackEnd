@props([
    'label' => null,
    'placeholder' => '',
    'hint' => null,
    'name' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 3,
])
<div>
    @if($label)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea
        @if($name) name="{{ $name }}" @endif
        @if($name) id="{{ $name }}" @endif
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'form-control-textarea']) }}
    >@if(!$attributes->has('x-model')){{ old($name, $value) }}@endif</textarea>
    @if($hint)
        <div class="text-xs text-gray-400 mt-1">{{ $hint }}</div>
    @endif
</div>
