<x-cn.button
    variant="warning"
    {{ $attributes }}>

    <i class="fas fa-edit"></i>

    {{ $slot->isEmpty() ? 'Editar' : $slot }}

</x-cn.button>
