@php
    $groupAttributes = $attributes
        ->merge(['class' => 'cn-group'])
        ->class([
            "cn-group-{$columns}" => $columns > 1,
        ]);
@endphp

<div
    @if($id)
        id="{{ $id }}"
    @endif

    {{ $groupAttributes }}
>
    {{ $slot }}
</div>
