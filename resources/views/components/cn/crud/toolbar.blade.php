@props([
    'title' => null,
])

<div {{ $attributes->class([
    'cn-crud__toolbar',
]) }}>

    @if ($title)

        <div class="cn-crud__toolbar-title">
            {{ $title }}
        </div>

    @endif


    <div class="cn-crud__toolbar-content">

        {{ $slot }}

    </div>

</div>
