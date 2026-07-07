@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'variant' => 'default',
    'padding' => true,
    'loading' => false,
    'collapsible' => false,
    'collapsed' => false,
])

@php

    $classes = collect(['cn-card', "cn-card-{$variant}"])->implode(' ');

@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>

    @if ($title || isset($actions))

        <div class="cn-card-header">

            <div class="cn-card-title">

                @if ($icon)
                    <i class="fas fa-{{ $icon }}"></i>
                @endif

                <div>

                    @if ($title)
                        <h5>{{ $title }}</h5>
                    @endif

                    @if ($subtitle)
                        <small>{{ $subtitle }}</small>
                    @endif

                </div>

            </div>

            @isset($actions)
                <div class="cn-card-actions">

                    {{ $actions }}

                </div>
            @endisset

        </div>

    @endif

    <div class="cn-card-body">

        {{ $slot }}

    </div>

    @isset($footer)
        <div class="cn-card-footer">

            {{ $footer }}

        </div>
    @endisset

</div>
