<x-layout.app>
    <x-slot:title>
        test_modules
    </x-slot:title>
    <div class="cn-page">

        <x-cn.crud title="test_modules" subtitle="Module TestModule " >

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.toolbar>
                    {{-- filtros dinámicos del módulo --}}
                    <x-cn.filters :action="route('test_modules.index')">
                        {{-- filtros personalizados --}}
                    </x-cn.filters>
                    <x-cn.button.create :href="route('test_modules.create')" />
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
                    @forelse ($test_modules  as $testModule)
                        <tr>
                            {{-- datos del módulo --}}
                            <td class="text-center">
                                {{-- acciones CRUD --}}
                            </td>
                            <td class="text-center">
                                <x-cn.crud.actions>
                                    {{-- show --}}
                                    <x-cn.button.show
                                        :href="route('test_modules.show', $testModule)" />
                                    {{-- edit --}}
                                    <x-cn.button.edit
                                        :href="route('test_modules.edit', $testModule)" />
                                    {{-- delete --}}
                                    <x-cn.confirm
                                        id="delete-testModule-{{ $testModule->id  }}"
                                        title="Eliminar test_module"
                                        message="¿Está seguro de eliminar este registro?">
                                        <form
                                            action="{{ route('test_modules.destroy', $[ [model ]]) }}"
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
                <x-cn.pagination :paginator="$test_modules" />
            </x-slot:footer>
        </x-cn.crud>
    </div>
</x-layout.app>
