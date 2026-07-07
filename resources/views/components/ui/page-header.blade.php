<div class="cn-page-header">

    <div class="cn-page-header-left">

        @if($icon)

            <div class="cn-page-icon">

                <i class="fas fa-{{ $icon }}"></i>

            </div>

        @endif

        <div>

            <h1 class="cn-page-title">

                {{ $title }}

            </h1>

            @if($subtitle)

                <p class="cn-page-subtitle">

                    {{ $subtitle }}

                </p>

            @endif

        </div>

    </div>

    @isset($actions)

        <div class="cn-page-actions">

            {{ $actions }}

        </div>

    @endisset

</div>