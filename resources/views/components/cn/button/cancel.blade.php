<x-cn.button
    variant="secondary"
    {{ $attributes }}>

    <i class="fas fa-times"></i>

    {{ $slot->isEmpty() ? 'Cancelar' : $slot }}

</x-cn.button>
