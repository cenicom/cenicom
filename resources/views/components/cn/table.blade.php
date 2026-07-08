@props([
    'responsive' => true,
])

<div class="cn-table-wrapper">

    @if($responsive)

        <div class="cn-table-responsive">

            <table {{ $attributes->merge([
                'class' => 'cn-table'
            ]) }}>

                {{ $slot }}

            </table>

        </div>

    @else

        <table {{ $attributes->merge([
            'class' => 'cn-table'
        ]) }}>

            {{ $slot }}

        </table>

    @endif

</div>
