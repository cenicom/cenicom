<x-cn.button
    variant="primary"
    type="submit"
    {{ $attributes }}>

    <i class="fas fa-save"></i>

    {{ $slot->isEmpty() ? 'Guardar' : $slot }}

</x-cn.button>
