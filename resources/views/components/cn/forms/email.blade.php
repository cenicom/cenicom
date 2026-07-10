<x-cn.input
    :name="$name"
    :id="$id"
    type="email"
    :value="$value"
    :placeholder="$placeholder"
    autocomplete="email"
    inputmode="email"
    spellcheck="false"
    :required="$required"
    :readonly="$readonly"
    :disabled="$disabled"
    :autofocus="$autofocus"
    :minlength="$minlength"
    :maxlength="$maxlength"

    {{ $attributes }}
/>
