<x-layout.app>
    <x-slot:title>
        prueba_texts
    </x-slot:title>

    <div class="cn-page">

        <x-cn.crud
            title="prueba_texts"
            subtitle="Module PruebaText">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.toolbar>

                    <x-cn.filters :action="route('prueba_texts.index')">
                        {{-- filtros personalizados --}}
                    </x-cn.filters>

                    <x-cn.button.create
                        :href="route('prueba_texts.create')" />

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

                    @forelse ($pruebaTexts as $pruebaText)

                        <tr>



                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('prueba_texts.show', $pruebaText)" />

                                    <x-cn.button.edit
                                        :href="route('prueba_texts.edit', $pruebaText)" />

                                    <x-cn.confirm
                                        id="delete-pruebaText-{{ $pruebaText->id }}"
                                        title="Eliminar prueba_text"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            action="{{ route('prueba_texts.destroy', $pruebaText) }}"
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
                    :paginator="$pruebaTexts" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
