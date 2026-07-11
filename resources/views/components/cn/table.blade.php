@props([
    'responsive' => true,
    'striped' => false,
    'hover' => true,
])

@php

    $classes = ['cn-table', 'cn-table--striped' => $striped, 'cn-table--hover' => $hover];

@endphp

@if ($responsive)
    <div class="cn-table-wrapper">
@endif


<table {{ $attributes->class($classes) }}>

    {{ $slot }}

</table>


@if ($responsive)
    </div>
@endif
