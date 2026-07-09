@php

$spacingClasses = [
    'compact'      => 'g-2',
    'normal'       => 'g-3',
    'comfortable'  => 'g-5',
];

@endphp

<div {{ $attributes->class([
    'row',
    $spacingClasses[$spacing] ?? $spacingClasses['normal'],
]) }}>

    {{ $slot }}

</div>
