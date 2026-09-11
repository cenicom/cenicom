<x-layout.app>
    <x-slot:title>
        currencies
    </x-slot:title>

    <div class="cn-page">

        <x-cn.crud
            title="currencies"
            subtitle="Currency module">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.crud.filters :action="route('admin.currencies.index')">
                        {{-- filtros personalizados --}}
                    </x-cn.crud.filters>

                    <x-cn.button.create
                        :href="route('admin.currencies.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>

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

                    @forelse ($currencies as $currency)

                        <tr>



                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('admin.currencies.show', $currency)" />

                                    <x-cn.button.edit
                                        :href="route('admin.currencies.edit', $currency)" />

                                    <x-cn.crud.confirm
                                        id="delete-currency-{{ $currency->id }}"
                                        title="Eliminar currency"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            action="{{ route('admin.currencies.destroy', $currency) }}"
                                            method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger">

                                                Confirmar

                                            </button>

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
                    :paginator="$currencies" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
