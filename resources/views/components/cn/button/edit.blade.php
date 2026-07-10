<x-cn.button
    variant="warning"
    {{ $attributes }}>

    <i class="fas fa-edit"></i>

    {{ $slot ?? 'Editar' }}

</x-cn.button>
