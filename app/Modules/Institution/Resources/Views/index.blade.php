<x-cn.crud
    title="Instituciones"
    subtitle="Listado de instituciones educativas"
    icon="bi bi-building"
>
    <x-slot:actions>
        @if ($permissions['create'])
            <x-cn.button.create
                :href="route('institutions.create')"
            >
                Crear institución
            </x-cn.button.create>
        @endif
    </x-slot:actions>

    <x-cn.table
        responsive
        :striped="false"
        :hover="true"
    >
        <x-slot:head>
            <x-cn.table.head>Código</x-cn.table.head>
            <x-cn.table.head>Código corto</x-cn.table.head>
            <x-cn.table.head>Nombre</x-cn.table.head>
            <x-cn.table.head>Estado</x-cn.table.head>
            <x-cn.table.head>Acciones</x-cn.table.head>
        </x-slot:head>

        <tbody>
            @forelse ($institutions as $institution)
                <x-cn.table.row>
                    <td>{{ $institution->code }}</td>
                    <td>{{ $institution->short_code }}</td>
                    <td>{{ $institution->name }}</td>
                    <td>{{ $institution->status }}</td>
                    <td>
                        @if ($permissions['update'])
                            <x-cn.button.edit
                                :href="route('institutions.edit', $institution)"
                            />
                        @endif

                        @if ($permissions['delete'])
                            <form
                                method="POST"
                                action="{{ route('institutions.destroy', $institution) }}"
                                class="d-inline"
                            >
                                @csrf
                                @method('DELETE')

                                <x-cn.button.delete />
                            </form>
                        @endif
                    </td>
                </x-cn.table.row>
            @empty
                <x-cn.table.row>
                    <td colspan="5">
                        No hay instituciones registradas.
                    </td>
                </x-cn.table.row>
            @endforelse
        </tbody>
    </x-cn.table>

    @if ($institutions->hasPages())
        <div class="mt-4">
            {{ $institutions->links() }}
        </div>
    @endif
</x-cn.crud>
