<x-layout.app>
    <x-slot:title>
        cn_generator_probes
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'cn_generator_probes', 'url' => null, 'current' => true],
        ]" />

        <x-cn.crud
            title="cn_generator_probes"
            subtitle="Module CnGeneratorProbe">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.button.create
                        :href="route('cn_generator_probes.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>
            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters :action="route('cn_generator_probes.index')">
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

                    @forelse ($cnGeneratorProbes as $cnGeneratorProbe)

                        <tr>



                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('cn_generator_probes.show', $cnGeneratorProbe)" />

                                    <x-cn.button.edit
                                        :href="route('cn_generator_probes.edit', $cnGeneratorProbe)" />

                                    <x-cn.crud.confirm
                                        id="delete-cnGeneratorProbe-{{ $cnGeneratorProbe->id }}"
                                        form-id="delete-cnGeneratorProbe-{{ $cnGeneratorProbe->id }}-form"
                                        title="Eliminar cn_generator_probe"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            id="delete-cnGeneratorProbe-{{ $cnGeneratorProbe->id }}-form"
                                            action="{{ route('cn_generator_probes.destroy', $cnGeneratorProbe) }}"
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
                    :paginator="$cnGeneratorProbes" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
