@props([
    'filters' => false,
])

<div {{ $attributes->merge(['class' => 'cn-toolbar']) }}>

    {{-- Barra principal --}}
    <div class="cn-toolbar-main">

        <div class="cn-toolbar-left">

            {{ $left ?? '' }}

        </div>

        <div class="cn-toolbar-center">

            {{ $center ?? '' }}

        </div>

        <div class="cn-toolbar-right">

            {{ $right ?? '' }}

        </div>

    </div>

    {{-- Área de filtros --}}

    @isset($filters)

        <div class="cn-toolbar-filters">

            {{ $filters }}

        </div>

    @endisset

</div>
