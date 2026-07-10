<div
    @if($id)
        id="{{ $id }}"
    @endif

    {{ $attributes->merge([
        'class' => 'cn-field',
    ]) }}
>
    {{ $slot }}
</div>
