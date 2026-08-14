<x-cn.button
    variant="info"
    {{ $attributes }}>

    <i class="fas fa-eye"></i>

    {{ $slot->isEmpty() ? 'Ver' : $slot }}

</x-cn.button>
