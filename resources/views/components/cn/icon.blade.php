@props([
    'name',
])

<i
    {{ $attributes->class([
        'fas',
        'fa-' . $name,
    ]) }}
></i>
