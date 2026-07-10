@php
    $message = $errors->first($for);

    $errorAttributes = $attributes->merge([
        'class' => 'cn-error',
    ]);
@endphp

@if($message)
    <div
        role="alert"
        aria-live="polite"
        {{ $errorAttributes }}
    >
        {{ $message }}
    </div>
@endif
