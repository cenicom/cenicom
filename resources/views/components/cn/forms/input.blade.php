@php
    $inputValue = old($name, $value);
    $isInvalid = $errors->has($name);

    $inputAttributes = $attributes->class(['cn-input', 'is-invalid' => $isInvalid]);
@endphp

<input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $inputValue }}"
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    @if ($inputmode) inputmode="{{ $inputmode }}" @endif
    @if ($pattern) pattern="{{ $pattern }}" @endif
    @if ($min) min="{{ $min }}" @endif
    @if ($max) max="{{ $max }}" @endif
    @if ($step) step="{{ $step }}" @endif
    @if ($minlength) minlength="{{ $minlength }}" @endif
    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
    aria-invalid="{{ $isInvalid ? 'true' : 'false' }}" @required($required) @readonly($readonly) @disabled($disabled)
    @autofocus($autofocus) {{ $inputAttributes }}
    aria-describedby="currency_code_error"
    data-cn="input">
