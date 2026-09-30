<x-cn.forms.input
    :name="$name"
    :id="$id"
    :type="$type"
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
