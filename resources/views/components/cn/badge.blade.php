@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php

$classes = [
    'cn-badge',
    "cn-badge--{$variant}",
    "cn-badge--{$size}",
];

@endphp


<span {{ $attributes->class($classes) }}>

    {{ $slot }}

</span>
