<x-cn.button
    variant="secondary"
    {{ $attributes }}>

    <i class="fas fa-times"></i>

    {{ $slot ?? 'Cancelar' }}

</x-cn.button>
