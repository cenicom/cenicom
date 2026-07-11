@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
])

@if ($title || $subtitle || $icon)

    <div class="cn-crud__header">

        @if ($icon)

            <i
                class="{{ $icon }} cn-crud__icon"
                aria-hidden="true"
            ></i>

        @endif


        <div class="cn-crud__heading">

            @if ($title)

                <h2 class="cn-crud__title">
                    {{ $title }}
                </h2>

            @endif


            @if ($subtitle)

                <p class="cn-crud__subtitle">
                    {{ $subtitle }}
                </p>

            @endif

        </div>

    </div>

@endif
