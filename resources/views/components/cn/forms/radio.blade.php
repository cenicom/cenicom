@props([
    'name',
    'value',
    'label',
    'checked'=>false,
])

<label class="cn-radio">

    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked(old($name)==$value || $checked)
    >

    <span>{{ $label }}</span>

</label>
