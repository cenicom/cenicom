@props([
    'create' => null,
])

@if ($create || trim($slot))

    <div {{ $attributes->class([
        'cn-crud__actions',
    ]) }}>

        @if ($create)

            <x-cn.button
                icon="fas fa-plus"
            >
                {{ $create }}
            </x-cn.button>

        @endif


        {{ $slot }}

    </div>

@endif
