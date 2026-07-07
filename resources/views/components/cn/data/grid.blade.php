@props([
    'loading' => false,
    'empty' => false,
])

<div {{ $attributes->merge(['class' => 'cn-data-grid']) }}>

    @isset($toolbar)

        <div class="cn-data-grid-toolbar">

            {{ $toolbar }}

        </div>

    @endisset

    @isset($filters)

        <div class="cn-data-grid-filters">

            {{ $filters }}

        </div>

    @endisset

    <div class="cn-data-grid-content">

        @if($loading)

            {{ $loading }}

        @elseif($empty)

            {{ $empty }}

        @else

            {{ $slot }}

        @endif

    </div>

    @isset($pagination)

        <div class="cn-data-grid-pagination">

            {{ $pagination }}

        </div>

    @endisset

</div>
