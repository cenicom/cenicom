<x-layout.app>
    <x-slot:title>
        states
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'states', 'url' => null, 'current' => true],
        ]" />

        <x-cn.crud
            title="states"
            subtitle="State module">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.button.create
                        :href="route('states.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>
            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters :action="route('states.index')">
                    {{-- filtros personalizados --}}
                </x-cn.crud.filters>

            </x-slot:filters>

            {{-- Tabla principal --}}
            <x-cn.table data-cn-datatable>

                <thead>
                    <tr>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Name
</th>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Country Id
</th>


                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($states as $state)

                        <tr>

<td>{{ $state->name }}</td>
<td>{{ $state->country_id }}</td>

                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('states.show', $state)" />

                                    <x-cn.button.edit
                                        :href="route('states.edit', $state)" />

                                    <x-cn.crud.confirm
                                        id="delete-state-{{ $state->id }}"
                                        form-id="delete-state-{{ $state->id }}-form"
                                        title="Eliminar state"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            id="delete-state-{{ $state->id }}-form"
                                            action="{{ route('states.destroy', $state) }}"
                                            method="POST">

                                            @csrf
                                            @method('DELETE')

                                        </form>

                                    </x-cn.crud.confirm>

                                </x-cn.crud.actions>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="3">

                                <x-cn.empty-state>
                                    No existen registros.
                                </x-cn.empty-state>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </x-cn.table>

            {{-- Footer --}}
            <x-slot:footer>

                <x-cn.crud.pagination
                    :paginator="$states" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
