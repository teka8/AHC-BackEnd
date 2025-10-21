@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => '',
    'hint' => null,
    'required' => false,
    'disabled' => false,
])
<div>
    @if($label)
        <label class="form-label" for="{{ $name }}">
            {{ $label }}
            @if($required) <span class="text-destructive">*</span> @endif
        </label>
    @endif

    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->class(['form-control', 'timepicker']) }}
        x-data
        x-init="
            flatpickr($el, {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'h:i K', // 12-hour format with AM/PM
                time_24hr: false,
                minuteIncrement: 1, // allow minutes from 00 to 59
            })
        "
        autocomplete="off"
    >

    @if($hint)
        <div class="text-xs text-gray-400 mt-1">{{ $hint }}</div>
    @endif
</div>
