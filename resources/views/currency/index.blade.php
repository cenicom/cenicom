<x-layout.app>

    <x-slot:title>
        Monedas
    </x-slot:title>


    <div class="cn-page">


        <x-cn.crud
            title="Monedas"
            subtitle="Administración y configuración de monedas del sistema."
            icon="fas fa-coins"
        >


            {{-- Toolbar --}}
            <x-slot:toolbar>

                <x-cn.crud.toolbar>

                    Herramientas del módulo

                </x-cn.crud.toolbar>

            </x-slot:toolbar>



            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters>

                    <form
                        method="GET"
                        action="{{ route('currencies.index') }}"
                        class="row g-3"
                    >

                        <div class="col-md-6">

                            <x-cn.input
                                name="search"
                                placeholder="Buscar..."
                                :value="request('search')"
                            />

                        </div>


                        <div class="col-md-3">

                            <x-cn.select name="status">

                                <option value="">
                                    Todos
                                </option>

                                <option
                                    value="1"
                                    @selected(request('status') == '1')
                                >
                                    Activas
                                </option>


                                <option
                                    value="0"
                                    @selected(request('status') == '0')
                                >
                                    Inactivas
                                </option>

                            </x-cn.select>

                        </div>


                        <div class="col-md-3 d-flex align-items-end gap-2">

                            <x-cn.button.search />

                            <x-cn.button.reset />

                        </div>


                    </form>


                </x-cn.crud.filters>

            </x-slot:filters>




            {{-- Actions --}}
            <x-slot:actions>

                <x-cn.crud.actions>

                    <x-cn.button.create
                        :href="route('currencies.create')"
                    />

                </x-cn.crud.actions>

            </x-slot:actions>




            {{-- Table --}}
            <x-cn.crud.table>


                <x-cn.table id="currency-table">


                    <thead>

                        <tr>

                            <th>
                                Código
                            </th>

                            <th>
                                Nombre
                            </th>

                            <th>
                                Símbolo
                            </th>

                            <th>
                                Estado
                            </th>

                            <th class="text-center">
                                Acciones
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                        @forelse ($currencies as $currency)


                            <tr>


                                <td>
                                    {{ $currency->code }}
                                </td>


                                <td>
                                    {{ $currency->name }}
                                </td>


                                <td>
                                    {{ $currency->symbol ?? '-' }}
                                </td>


                                <td>

                                    @if ($currency->status)

                                        <x-cn.badge variant="success">
                                            Activa
                                        </x-cn.badge>

                                    @else

                                        <x-cn.badge variant="secondary">
                                            Inactiva
                                        </x-cn.badge>

                                    @endif

                                </td>



                                <td class="text-center">


                                    <x-cn.crud.actions>


                                        <x-cn.button.show
                                            :href="route('currencies.show', $currency)"
                                        />


                                        <x-cn.button.edit
                                            :href="route('currencies.edit', $currency)"
                                        />



                                        <x-cn.confirm
                                            id="delete-currency-{{ $currency->id }}"
                                            title="Eliminar moneda"
                                            message="¿Está seguro de eliminar la moneda {{ $currency->name }}?"
                                        >

                                            <form
                                                action="{{ route('currencies.destroy', $currency) }}"
                                                method="POST"
                                            >

                                                @csrf

                                                @method('DELETE')


                                                <button
                                                    type="submit"
                                                    class="btn btn-danger"
                                                >
                                                    Confirmar
                                                </button>


                                            </form>


                                        </x-cn.confirm>


                                    </x-cn.crud.actions>


                                </td>


                            </tr>


                        @empty


                            <tr>

                                <td colspan="5">

                                    <x-cn.empty-state>

                                        No existen monedas registradas.

                                    </x-cn.empty-state>

                                </td>

                            </tr>


                        @endforelse


                    </tbody>


                </x-cn.table>


            </x-cn.crud.table>




            {{-- Footer --}}
            <x-slot:footer>

                <x-cn.pagination
                    :paginator="$currencies"
                />

            </x-slot:footer>



        </x-cn.crud>



    </div>




    {{-- Modal fuera de la tabla --}}

    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#currency-example-modal"
    >

        Abrir modal

    </button>



    <x-cn.modal
        id="currency-example-modal"
        title="Ejemplo modal"
    >

        <x-slot:body>

            Contenido del modal CENICOM UI Framework.

        </x-slot:body>


        <x-slot:footer>

            <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
            >

                Cerrar

            </button>

        </x-slot:footer>


    </x-cn.modal>


</x-layout.app>
