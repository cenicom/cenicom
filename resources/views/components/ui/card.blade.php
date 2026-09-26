<div {{ $attributes->merge(['class' => 'cn-card']) }}>

    @if($title || isset($header))

        <div class="cn-card-header">

            @isset($header)

                {{ $header }}

            @else

                <div class="d-flex align-items-center gap-2">

                    @if($icon)

                        <i class="fas fa-{{ $icon }}"></i>

                    @endif

                    <span>{{ $title }}</span>

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