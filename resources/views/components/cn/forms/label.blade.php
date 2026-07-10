@php
    $labelAttributes = $attributes->merge([
        'class' => 'cn-label',
    ]);
@endphp

<label
    @if($for)
        for="{{ $for }}"
    @endif

    {{ $labelAttributes }}
>
    {{ $slot }}

    @if($required)
        <span
            class="cn-label-required"
            aria-hidden="true"
        >
            *
        </span>
    @endif
</label>
