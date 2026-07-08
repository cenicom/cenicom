@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "cn-btn cn-btn--{$variant} cn-btn--{$size}"
    ]) }}
>

    @if($icon)

        <i class="fas fa-{{ $icon }}"></i>

    @endif

    <span>

        {{ $slot }}

    </span>

</button>
