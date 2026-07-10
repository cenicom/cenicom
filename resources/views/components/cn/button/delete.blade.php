<x-cn.button
    variant="danger"
    type="submit"
    {{ $attributes }}>

    <i class="fas fa-trash"></i>

    {{ $slot ?? 'Eliminar' }}

</x-cn.button>
