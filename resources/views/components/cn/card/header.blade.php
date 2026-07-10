@props([
    'title' => null,
    'icon' => null,
])

<header {{ $attributes->class(['cn-card-header']) }}>

    @if($title || $icon)

        <div class="cn-card-header__content">

            @if($icon)

                <x-cn.icon :name="$icon" />

            @endif

            @if($title)

                <h2 class="cn-card-header__title">

                    {{ $title }}

                </h2>

            @endif

        </div>

    @endif

    {{ $slot }}

</header>
