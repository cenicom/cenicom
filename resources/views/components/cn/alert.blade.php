@props([
    'type' => 'info',
    'title' => null,
])

<div {{ $attributes->merge(['class' => "cn-alert cn-alert-{$type}"]) }}>

    @if($title)

        <strong>{{ $title }}</strong>

    @endif

    <div>

        {{ $slot }}

    </div>

</div>
