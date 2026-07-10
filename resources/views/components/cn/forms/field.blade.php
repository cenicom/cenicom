@props([
    'name',
    'label',
    'for' => null,
    'required' => false,
    'hint' => null,
    'optional' => false,
])

@php
    $fieldId = $for ?? $name;
@endphp

<div {{ $attributes->class('cn-field') }}>

    <label
        class="cn-field__label"
        for="{{ $fieldId }}">

        <span>{{ $label }}</span>

        @if($required)

            <span class="cn-field__required">*</span>

        @elseif($optional)

            <span class="cn-field__optional">
                (Opcional)
            </span>

        @endif

    </label>

    <div class="cn-field__control">

        {{ $slot }}

    </div>

    @if($hint)

        <div
            id="{{ $fieldId }}-hint"
            class="cn-field__hint">

            {{ $hint }}

        </div>

    @endif

    @error($name)

        <div
            id="{{ $fieldId }}-error"
            class="cn-field__error">

            {{ $message }}

        </div>

    @enderror

</div>
