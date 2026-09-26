<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}

    {{ $attributes->merge([
        'class' => "cn-btn cn-btn-{$variant} cn-btn-{$size}"
    ]) }}>

    @if($icon)

        <i class="fas fa-{{ $icon }} me-2"></i>

    @endif

    {{ $slot }}

</button>