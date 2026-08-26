<x-layout.app>
    <x-slot:title>
        generator_probes
    </x-slot:title>

    <div class="cn-page">

        <x-cn.crud
            title="generator_probes"
            subtitle="Module GeneratorProbe">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.toolbar>

                    <x-cn.filters :action="route('generator_probes.index')">
                        {{-- filtros personalizados --}}
                    </x-cn.filters>

                    <x-cn.button.create
                        :href="route('generator_probes.create')" />

                </x-cn.toolbar>
            </x-slot:toolbar>

            {{-- Tabla principal --}}
            <x-cn.table>

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

                                    <x-cn.confirm
                                        id="delete-generatorProbe-{{ $generatorProbe->id }}"
                                        title="Eliminar generator_probe"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            action="{{ route('generator_probes.destroy', $generatorProbe) }}"
                                            method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger">

                                                Confirmar

                                            </button>

                                        </form>

                                    </x-cn.confirm>

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

                <x-cn.pagination
                    :paginator="$generatorProbes" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
