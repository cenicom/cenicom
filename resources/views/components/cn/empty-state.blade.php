@props([
    'title' => null,
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->class('cn-empty-state') }}>

    @if($icon)
        <div class="cn-empty-state__icon">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
    @endif

    @if($title)
        <h3 class="cn-empty-state__title">
            {{ $title }}
        </h3>
    @endif

    @if($description)
        <p class="cn-empty-state__description">
            {{ $description }}
        </p>
    @endif

    @if($slot->isNotEmpty())
        <div class="cn-empty-state__content">
            {{ $slot }}
        </div>
    @endif

    @isset($actions)
        <div class="cn-empty-state__actions">
            {{ $actions }}
        </div>
    @endisset

</div>
