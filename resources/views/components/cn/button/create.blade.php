<x-cn.button
    variant="primary"
    {{ $attributes }}>

    <i class="fas fa-plus"></i>

    {{ $slot->isEmpty() ? 'Nuevo' : $slot }}

</x-cn.button>
