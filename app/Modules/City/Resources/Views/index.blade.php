<x-layout.app>
    <x-slot:title>
        cities
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'cities', 'url' => null, 'current' => true],
        ]" />

        <x-cn.crud
            title="cities"
            subtitle="City module">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.button.create
                        :href="route('cities.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>
            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters :action="route('cities.index')">
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
    State Id
</th>


                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($cities as $city)

                        <tr>

<td>{{ $city->name }}</td>
<td>{{ $city->state_id }}</td>

                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('cities.show', $city)" />

                                    <x-cn.button.edit
                                        :href="route('cities.edit', $city)" />

                                    <x-cn.crud.confirm
                                        id="delete-city-{{ $city->id }}"
                                        form-id="delete-city-{{ $city->id }}-form"
                                        title="Eliminar city"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            id="delete-city-{{ $city->id }}-form"
                                            action="{{ route('cities.destroy', $city) }}"
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
                    :paginator="$cities" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
