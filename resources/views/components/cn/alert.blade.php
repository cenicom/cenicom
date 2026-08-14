@props([
    'type' => 'info',
])

<div {{ $attributes->merge([
    'class' => 'cn-alert cn-alert-' . $type,
]) }}>
    {{ $slot }}
</div>
