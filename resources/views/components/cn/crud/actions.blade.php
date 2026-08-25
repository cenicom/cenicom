@props([
    'actions' => [],
    'create' => null,
])

@if (count($actions) || $create || trim($slot))

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

        @foreach ($actions as $action)

            <x-cn.button
                :href="$action->href"
                :variant="$action->variant"
                :size="$action->size"
            >
                @if ($action->icon)
                    <i
                        class="{{ $action->icon }}"
                        aria-hidden="true"
                    ></i>
                @endif

                {{ $action->label }}
            </x-cn.button>

        @endforeach

        {{ $slot }}

    </div>

@endif
