<x-cn.button
    variant="primary"
    {{ $attributes }}>

    <i class="fas fa-plus"></i>

    {{ $slot ?? 'Nuevo' }}

</x-cn.button>
