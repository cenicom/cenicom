<x-cn.button
    variant="primary"
    type="submit"
    {{ $attributes }}>

    <i class="fas fa-save"></i>

    {{ $slot ?? 'Guardar' }}

</x-cn.button>
