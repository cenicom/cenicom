<x-cn.input
    :name="$name"
    :id="$id"
    :value="$value"
    :placeholder="$placeholder"
    :min="$min"
    :max="$max"
    :step="$step"
    :pattern="$pattern"
    :inputmode="$inputmode"
    :required="$required"
    :readonly="$readonly"
    :disabled="$disabled"
    :autofocus="$autofocus"
    {{ $attributes }}
/>
