@props([
    'name',
    'type' => 'text',
    'value' => '',
])

<input
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value) }}"

    {{ $attributes->merge([
        'class' => 'cn-input'
    ]) }}
>
