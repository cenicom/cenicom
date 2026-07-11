<div
    @isset($id)
        id="{{ $id }}"
    @endisset

    data-cn="field"

    {{ $attributes->class([
        'cn-field',
    ]) }}
>
    {{ $slot }}
</div>
