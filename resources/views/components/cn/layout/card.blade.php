@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'cn-card']) }}>

    @if($title || $subtitle || $icon)

        <div class="cn-card__header">

            <div class="cn-card__title">

                @if($icon)
                    <i class="fas fa-{{ $icon }}"></i>
                @endif

                <div>

                    @if($title)
                        <h2>{{ $title }}</h2>
                    @endif

                    @if($subtitle)
                        <p>{{ $subtitle }}</p>
                    @endif

                </div>

            </div>

            @isset($actions)

                <div class="cn-card__actions">

                    {{ $actions }}

                </div>

            @endisset

        </div>

    @endif

    <div class="cn-card__body">

        {{ $slot }}

    </div>

    @isset($footer)

        <div class="cn-card__footer">

            {{ $footer }}

        </div>

    @endisset

</div>
