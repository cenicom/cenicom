<x-cn.checkbox
    :name="$name"
    :id="$id"
    :value="$value"
    :checked="$checked"
    :required="$required"
    :disabled="$disabled"
    {{ $attributes->class([
        'cn-switch',
    ]) }}
/>
