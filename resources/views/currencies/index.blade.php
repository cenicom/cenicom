<x-layout.app>
    <x-slot:title>
        currencies
    </x-slot:title>

    <div class="cn-page">

        <x-cn.crud
            title="currencies"
            subtitle="Module Currency">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.toolbar>

                    <x-cn.filters :action="route('currencies.index')">
                        {{-- filtros personalizados --}}
                    </x-cn.filters>

                    <x-cn.button.create
                        :href="route('currencies.create')" />

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

                    @forelse ($currencies as $currency)

                        <tr>



                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('currencies.show', $currency)" />

                                    <x-cn.button.edit
                                        :href="route('currencies.edit', $currency)" />

                                    <x-cn.confirm
                                        id="delete-currency-{{ $currency->id }}"
                                        title="Eliminar currency"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            action="{{ route('currencies.destroy', $currency) }}"
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
                    :paginator="$currencies" />

            </x-slot:footer>

        </x-cn.crud>

    </div>

</x-layout.app>
