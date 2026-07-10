@php
    $textareaValue = old($name, $value);
    $isInvalid = $errors->has($name);

    $textareaAttributes = $attributes
        ->merge([
            'class' => 'cn-textarea',
        ])
        ->class([
            'is-invalid' => $isInvalid,
        ]);
@endphp

<textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
    @if ($minlength) minlength="{{ $minlength }}" @endif
    aria-invalid="{{ $isInvalid ? 'true' : 'false' }}" @required($required) @readonly($readonly) @disabled($disabled)
    @autofocus($autofocus) {{ $textareaAttributes }}>
{{ $textareaValue }}
</textarea>
