@php
    $helpAttributes = $attributes->merge([
        'class' => 'cn-help',
    ]);
@endphp

<small
    @isset($id)
        id="{{ $id }}"
    @endisset

    {{ $attributes->class([
        'cn-help',
    ]) }}
>
    {{ $slot }}
</small>
