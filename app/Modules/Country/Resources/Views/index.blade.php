<x-layout.app>
    <x-slot:title>
        countries
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'countries', 'url' => null, 'current' => true],
        ]" />

        <x-cn.crud
            title="countries"
            subtitle="Country module">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.button.create
                        :href="route('countries.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>
            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters :action="route('countries.index')">
                    {{-- filtros personalizados --}}
                </x-cn.crud.filters>

            </x-slot:filters>

            {{-- Tabla principal --}}
            <x-cn.table data-cn-datatable>

                <thead>
                    <tr>



                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($countries as $country)

                        <tr>



                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('countries.show', $country)" />

                                    <x-cn.button.edit
                                        :href="route('countries.edit', $country)" />

                                    <x-cn.crud.confirm
                                        id="delete-country-{{ $country->id }}"
                                        form-id="delete-country-{{ $country->id }}-form"
                                        title="Eliminar country"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            id="delete-country-{{ $country->id }}-form"
                                            action="{{ route('countries.destroy', $country) }}"
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

                            <td colspan="1">

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
                    :paginator="$countries" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
