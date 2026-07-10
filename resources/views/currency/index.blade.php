<x-layout.app>

    <x-slot name="title">
        Monedas
    </x-slot>


    <div class="cn-page">


        <x-cn.page-header title="Monedas" description="Administración y configuración de monedas del sistema."
            icon="coins">

            <x-cn.button.create :href="route('currencies.create')">

                Nueva moneda

            </x-cn.button.create>

        </x-cn.page-header>



        <x-cn.card>


            <x-cn.table id="currency-table">


                <thead>

                    <tr>

                        <th>Código</th>

                        <th>Nombre</th>

                        <th>Símbolo</th>

                        <th>Estado</th>

                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($currencies as $currency)
                        <tr>

                            <td>{{ $currency->code }}</td>

                            <td>{{ $currency->name }}</td>

                            <td>{{ $currency->symbol ?? '-' }}</td>


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


                            <td>

                                <x-cn.button.show :href="route('currencies.show', $currency)" />

                                <x-cn.button.edit :href="route('currencies.edit', $currency)" />

                                <form action="{{ route('currencies.destroy', $currency) }}" method="POST">

                                    @csrf

                                    @method('DELETE')

                                    <x-cn.button.delete />

                                </form>

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


            {{ $currencies->links() }}


        </x-cn.card>


    </div>


</x-layout.app>
