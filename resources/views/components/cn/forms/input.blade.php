@php
    $inputId = $id;
    $inputValue = old($name, $value);

    $inputClass = $attributes->class(['cn-input', 'is-invalid' => $errors->has($name)]);
@endphp

<input
    id="{{ $inputId }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ $inputValue }}"
    placeholder="{{ $placeholder }}"
    autocomplete="{{ $autocomplete }}"
    min="{{ $min }}"
    max="{{ $max }}"
    step="{{ $step }}"
    minlength="{{ $minlength }}"
    maxlength="{{ $maxlength }}"
    $isInvalid = $errors->has($name);
    aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
    @required($required)
    @readonly($readonly)
    @disabled($disabled)
    @autofocus($autofocus)

    $attributes = $attributes
        ->except('class')
        ->class([
            'cn-input',
            'is-invalid' => $isInvalid,
    ]);
>
