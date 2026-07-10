@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $classes = [
        'cn-button',
        "cn-button--{$variant}",
        "cn-button--{$size}",
    ];

    if ($disabled) {
        $classes[] = 'cn-button--disabled';
    }
@endphp


@if($href)

    <a
        href="{{ $disabled ? '#' : $href }}"
        {{ $attributes->class($classes) }}
        @if($disabled)
            aria-disabled="true"
            tabindex="-1"
        @endif
    >

        {{ $slot }}

    </a>


@else


    <button
        type="{{ $type }}"
        {{ $attributes->class($classes) }}
        @disabled($disabled)
    >

        {{ $slot }}

    </button>


@endif
