@props([
    'title',
    'subtitle' => null,
    'icon' => null,
])

<div class="cn-page-header">

    <div class="cn-page-header__content">

        @if($icon)
            <div class="cn-page-header__icon">
                <i class="fas fa-{{ $icon }}"></i>
            </div>
        @endif

        <div class="cn-page-header__text">

            <h1 class="cn-page-header__title">

                {{ $title }}

            </h1>

            @if($subtitle)

                <p class="cn-page-header__subtitle">

                    {{ $subtitle }}

                </p>

            @endif

        </div>

    </div>

    @isset($actions)

        <div class="cn-page-header__actions">

            {{ $actions }}

        </div>

    @endisset

</div>
