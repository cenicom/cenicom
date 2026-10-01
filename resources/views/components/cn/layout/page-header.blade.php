@props([
    'title' => '',
    'subtitle' => '',
    'icon' => '',
])

<header {{ $attributes->class(['cn-page-header']) }}>

    @if ($icon !== '')
        <span class="cn-page-header__icon" aria-hidden="true">
            {{ $icon }}
        </span>
    @endif

    <div class="cn-page-header__content">

        <h1 class="cn-page-title">
            {{ $title }}
        </h1>

        @if ($subtitle !== '')
            <div class="cn-page-header__subtitle">
                {{ $subtitle }}
            </div>
        @endif

    </div>

</header>
