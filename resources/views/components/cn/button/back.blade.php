@props([
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $classes = [
        'cn-button',
        'cn-button--secondary',
        'cn-button--md',
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
        <i class="fas fa-arrow-left"></i>

        {{ $slot->isEmpty() ? 'Atrás' : $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->class($classes) }}
        @disabled($disabled)
        @if($disabled)
            aria-disabled="true"
        @endif
    >
        <i class="fas fa-arrow-left"></i>

        {{ $slot->isEmpty() ? 'Atrás' : $slot }}
    </button>
@endif
