@php
    $selected = old($name, $value);
    $isInvalid = $errors->has($name);

    $selectAttributes = $attributes->class([
        'cn-select',
        'is-invalid' => $isInvalid,
    ]);
@endphp

<select
    id="{{ $id }}"
    name="{{ $multiple ? $name . '[]' : $name }}"
    aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"
    @multiple($multiple)
    @required($required)
    @disabled($disabled)
    {{ $selectAttributes }}
>

    @if($placeholder)
        <option
            value=""
            @if($required) disabled @endif
            @selected(empty($selected))
        >
            {{ $placeholder }}
        </option>
    @endif

    @foreach($options as $key => $label)

        <option
            value="{{ $key }}"
            @selected(
                $multiple
                    ? in_array($key, (array) $selected, true)
                    : $selected == $key
            )
        >
            {{ $label }}
        </option>

    @endforeach

</select>
