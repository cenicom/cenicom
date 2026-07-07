@props([
    'action' => null,
    'label' => null,
    'icon' => null,
    'variant' => null,
    'type' => null,
    'size' => 'md',
    'href' => null,
    'disabled' => false,
])

@php

$config = $action
    ? config("cn-actions.$action", [])
    : [];

$label = $label ?? $config['label'] ?? '';
$icon = $icon ?? $config['icon'] ?? null;
$variant = $variant ?? $config['variant'] ?? 'primary';
$type = $type ?? $config['type'] ?? 'button';

$classes = collect([
    'cn-btn',
    "cn-btn-$variant",
    "cn-btn-$size",
])->implode(' ');

@endphp
