@php
    $hintAttributes = $attributes->merge([
        'class' => 'cn-hint',
    ]);
@endphp

<small
    @if($id)
        id="{{ $id }}"
    @endif

    {{ $hintAttributes }}
>
    {{ $slot }}
</small>
