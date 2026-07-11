@props([
    'title' => null,
])

<div {{ $attributes->class([
    'cn-crud__filters',
]) }}>

    @if ($title)

        <div class="cn-crud__filters-title">
            {{ $title }}
        </div>

    @endif


    <div class="cn-crud__filters-content">

        {{ $slot }}

    </div>

</div>
