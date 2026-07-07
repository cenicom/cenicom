@props([
    'name'=>'search',
    'placeholder'=>'Buscar...',
])

<div class="cn-search">

    <i class="fas fa-search"></i>

    <input
        type="search"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class'=>'cn-search-input'
        ]) }}
    >

</div>
