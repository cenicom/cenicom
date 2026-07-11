@php
    $message = $errors->first($for);

    $errorAttributes = $attributes->class([
        'cn-error',
    ]);
@endphp

@if($message)
    <div
        id="{{ $for }}-error"
        role="alert"
        aria-live="polite"

        {{ $errorAttributes }}
    >
        {{ $message }}
    </div>
@endif
