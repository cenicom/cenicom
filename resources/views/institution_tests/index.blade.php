<x-layout.app>
    <x-slot:title>
        institution_tests
    </x-slot:title>
    <div class="cn-page">

        <x-cn.crud title="institution_tests" subtitle="Module InstitutionTest " >

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.toolbar>
                    {{-- filtros dinámicos del módulo --}}
                    <x-cn.filters :action="route('institution_tests.index')">
                        {{-- filtros personalizados --}}
                    </x-cn.filters>
                    <x-cn.button.create :href="route('institution_tests.create')" />
                </x-cn.toolbar>
            </x-slot:toolbar>
            {{-- Tabla principal --}}
            <x-cn.table>
                <thead>
                    <tr>
                        {{-- columnas del módulo --}}
                        <th class="text-center">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($institution_tests  as $institutionTest)
                        <tr>
                            {{-- datos del módulo --}}
                            <td class="text-center">
                                {{-- acciones CRUD --}}
                            </td>
                            <td class="text-center">
                                <x-cn.crud.actions>
                                    {{-- show --}}
                                    <x-cn.button.show
                                        :href="route('institution_tests.show', $institutionTest)" />
                                    {{-- edit --}}
                                    <x-cn.button.edit
                                        :href="route('institution_tests.edit', $institutionTest)" />
                                    {{-- delete --}}
                                    <x-cn.confirm
                                        id="delete-institutionTest-{{ $institutionTest->id  }}"
                                        title="Eliminar institution_test"
                                        message="¿Está seguro de eliminar este registro?">
                                        <form
                                            action="{{ route('institution_tests.destroy', $[ [model ]]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                Confirmar
                                            </button>
                                        </form>
                                    </x-cn.confirm>
                                </x-cn.crud.actions>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="0">
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
                <x-cn.pagination :paginator="$institution_tests" />
            </x-slot:footer>
        </x-cn.crud>
    </div>
</x-layout.app>
