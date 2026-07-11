@php
    $isChecked = old($name, $checked);
    $isInvalid = $errors->has($name);

    $checkboxAttributes = $attributes->class([
        'cn-checkbox',
        'is-invalid' => $isInvalid,
    ]);
@endphp

<input
    type="checkbox"
    id="{{ $id }}"
    name="{{ $name }}"
    value="{{ $value }}"

    @checked($isChecked)

    @required($required)
    @disabled($disabled)

    aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"

    {{ $checkboxAttributes }}
>
