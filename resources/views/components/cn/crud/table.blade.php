@props([
    'title' => null,
    'empty' => null,
])

<div {{ $attributes->class([
    'cn-crud__table',
]) }}>

    @if ($title)

        <div class="cn-crud__table-title">
            {{ $title }}
        </div>

    @endif


    <div class="cn-crud__table-content">

        @if ($slot->isNotEmpty())

            {{ $slot }}

        @elseif ($empty)

            <div class="cn-crud__table-empty">
                {{ $empty }}
            </div>

        @endif

    </div>

</div>
