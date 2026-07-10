@php
    $actionsAttributes = $attributes
        ->merge([
            'class' => 'cn-actions',
        ])
        ->class([
            "cn-actions-{$align}" => in_array($align, ['start', 'center', 'end', 'between']),
        ]);
@endphp

<div
    @if($id)
        id="{{ $id }}"
    @endif

    {{ $actionsAttributes }}
>
    {{ $slot }}
</div>
