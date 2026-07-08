@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => 'Seleccione una opción...',
])

<select
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'cn-select'
    ]) }}
>

    <option value="">

        {{ __($placeholder) }}

    </option>

    @foreach($options as $key => $text)

        <option
            value="{{ $key }}"
            @selected(old($name, $value) == $key)
        >

            {{ $text }}

        </option>

    @endforeach

</select>
