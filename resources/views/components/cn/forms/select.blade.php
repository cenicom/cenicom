@php
    $selected = old($name, $value);
    $isInvalid = $errors->has($name);

    $selectAttributes = $attributes
        ->merge([
            'class' => 'cn-select',
        ])
        ->class([
            'is-invalid' => $isInvalid,
        ]);
@endphp

<select
    id="{{ $id }}"
    name="{{ $multiple ? $name.'[]' : $name }}"
    @multiple($multiple)
    @required($required)
    @disabled($disabled)
    {{ $selectAttributes }}
>

@if($placeholder)
    <option value="">
        {{ $placeholder }}
    </option>
@endif

@foreach($options as $key => $label)

<option
    value="{{ $key }}"
    @selected($selected == $key)
>
    {{ $label }}
</option>

@endforeach

</select>
