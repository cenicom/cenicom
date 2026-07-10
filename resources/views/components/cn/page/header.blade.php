<header {{ $attributes->class(['cn-page-header']) }}>

    <div class="cn-page-title">

        @if($icon)
            <div class="cn-page-icon">
                <x-cn.icon :name="$icon" />
            </div>
        @endif

        <div class="cn-page-heading">

            <h1 class="cn-page-heading__title">
                {{ $title }}
            </h1>

            @if($description)
                <p class="cn-page-heading__description">
                    {{ $description }}
                </p>
            @endif

        </div>

    </div>

    @isset($actions)

        <div class="cn-page-actions">

            {{ $actions }}

        </div>

    @endisset

</header>

@if($divider)
    <hr class="cn-page-divider">
@endif
