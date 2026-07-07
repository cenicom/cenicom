@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
    'block' => false,
    'disabled' => false,
    'loading' => false,
    'iconPosition' => 'left',
])

@php

$classes = collect([

    'cn-button',

    "cn-button-{$variant}",

    "cn-button-{$size}",

    $block ? 'cn-button-block' : '',

])->implode(' ');

@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
>

    @if($icon)
        <i class="fas fa-{{ $icon }}"></i>
    @endif

    {{ $slot }}

</button>
