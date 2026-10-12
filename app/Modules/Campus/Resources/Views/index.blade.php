<x-layout.app>
    <x-cn.navigation.breadcrumb :items="[
        [
            'label' => 'Inicio',
            'url' => route('dashboard'),
        ],
        [
            'label' => 'Sedes educativas',
            'current' => true,
        ],
    ]" />
    <x-cn.crud title="Sedes educativas" subtitle="Listado de sedes registradas" icon="bi bi-building">
        <x-slot:actions>
            @if ($permissions['create'])
                <x-cn.button.create :href="route('campuses.create')">
                    Crear sede
                </x-cn.button.create>
            @endif
        </x-slot:actions>

        <x-cn.table responsive :striped="false" :hover="true">
            <x-slot:head>
                <x-cn.table.head>Código</x-cn.table.head>
                <x-cn.table.head>Código corto</x-cn.table.head>
                <x-cn.table.head>Nombre</x-cn.table.head>
                <x-cn.table.head>Institución</x-cn.table.head>
                <x-cn.table.head>Estado</x-cn.table.head>
            </x-slot:head>

            <tbody>
                @forelse ($campuses as $campus)
                    <x-cn.table.row>
                        <td>{{ $campus->code }}</td>
                        <td>{{ $campus->short_code }}</td>
                        <td>{{ $campus->name }}</td>
                        <td>
                            {{ $campus->institution?->name ?? 'No disponible' }}
                        </td>
                        <td>{{ $campus->status }}</td>
                    </x-cn.table.row>
                @empty
                    <x-cn.table.row>
                        <td colspan="5">
                            No hay sedes registradas.
                        </td>
                    </x-cn.table.row>
                @endforelse
            </tbody>
        </x-cn.table>

        @if ($campuses->hasPages())
            <div class="mt-4">
                {{ $campuses->links() }}
            </div>
        @endif
    </x-cn.crud>
</x-layout.app>
