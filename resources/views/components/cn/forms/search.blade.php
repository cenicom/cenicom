<x-cn.input
    :name="$name"
    :id="$id"
    type="search"
    :value="$value"
    :placeholder="$placeholder"
    autocomplete="off"
    spellcheck="false"
    :required="$required"
    :readonly="$readonly"
    :disabled="$disabled"
    :autofocus="$autofocus"
    {{ $attributes }}
/>
