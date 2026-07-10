@php
    $helpAttributes = $attributes->merge([
        'class' => 'cn-help',
    ]);
@endphp

<small
    @if($id)
        id="{{ $id }}"
    @endif

    {{ $helpAttributes }}
>
    {{ $slot }}
</small>
