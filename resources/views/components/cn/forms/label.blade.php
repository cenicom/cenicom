<label
    @isset($for)
        for="{{ $for }}"
    @endisset

    {{ $attributes->class([
        'cn-label',
    ]) }}
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
