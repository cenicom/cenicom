@props([
    'name',
    'label',
    'checked'=>false,
])

<label class="cn-checkbox">

    <input
        type="checkbox"
        name="{{ $name }}"
        value="1"
        @checked(old($name,$checked))
    >

    <span>{{ $label }}</span>

</label>
