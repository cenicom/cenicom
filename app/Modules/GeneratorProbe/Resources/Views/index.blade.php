<x-layout.app>
    <x-slot:title>
        generator_probes
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'generator_probes', 'url' => null, 'current' => true],
        ]" />

        <x-cn.crud
            title="generator_probes"
            subtitle="Module GeneratorProbe">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.button.create
                        :href="route('generator_probes.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>
            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters :action="route('generator_probes.index')">
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

                    @forelse ($generatorProbes as $generatorProbe)

                        <tr>



                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('generator_probes.show', $generatorProbe)" />

                                    <x-cn.button.edit
                                        :href="route('generator_probes.edit', $generatorProbe)" />

                                    <x-cn.crud.confirm
                                        id="delete-generatorProbe-{{ $generatorProbe->id }}"
                                        form-id="delete-generatorProbe-{{ $generatorProbe->id }}-form"
                                        title="Eliminar generator_probe"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            id="delete-generatorProbe-{{ $generatorProbe->id }}-form"
                                            action="{{ route('generator_probes.destroy', $generatorProbe) }}"
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
                    :paginator="$generatorProbes" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
