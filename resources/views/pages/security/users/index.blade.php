<x-cn.patterns.crud
    title="Usuarios"
    subtitle="Administración de usuarios del sistema">

    <x-slot:actions>

        <x-cn.button icon="plus">

            Nuevo Usuario

        </x-cn.button>

    </x-slot:actions>

    <x-slot:filters>

        <x-cn.forms.input
            name="search"
            placeholder="Buscar usuario..."
            icon="magnifying-glass" />

        <x-cn.forms.select
            name="status">

            <option value="">Estado</option>
            <option>Activo</option>
            <option>Inactivo</option>

        </x-cn.forms.select>

        <x-cn.forms.select
            name="role">

            <option value="">Rol</option>

        </x-cn.forms.select>

    </x-slot:filters>

    <x-cn.card>

        <x-cn.data.table>

            {{-- Aquí irá la tabla --}}

        </x-cn.data.table>

    </x-cn.card>

</x-cn.patterns.crud>
