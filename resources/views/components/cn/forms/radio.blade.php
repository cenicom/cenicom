@php
    $selected = old($name, $checked ? $value : null);
    $isInvalid = $errors->has($name);

    $radioAttributes = $attributes->class([
        'cn-radio',
        'is-invalid' => $isInvalid,
    ]);
@endphp

<input
    type="radio"
    id="{{ $id }}"
    name="{{ $name }}"
    value="{{ $value }}"

    @checked($selected == $value)

    @required($required)
    @disabled($disabled)

    aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"

    {{ $radioAttributes }}
>
