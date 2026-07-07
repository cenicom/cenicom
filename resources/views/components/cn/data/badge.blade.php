@props([
    'variant' => 'secondary',
    'pill' => true,
])

@php
    $classes = [
        'cn-badge',
        "cn-badge-{$variant}",
        $pill ? 'cn-badge-pill' : '',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => implode(' ', array_filter($classes))
]) }}>
    {{ $slot }}
</span>
