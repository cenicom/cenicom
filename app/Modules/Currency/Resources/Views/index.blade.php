<x-layout.app>
    <x-slot:title>
        currencies
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'currencies', 'url' => null, 'current' => true],
        ]" />

        <x-cn.crud
            title="currencies"
            subtitle="Module Currency">

            {{-- Toolbar --}}
            <x-slot:toolbar>
                <x-cn.crud.toolbar>

                    <x-cn.button.create
                        :href="route('currencies.create')" />

                </x-cn.crud.toolbar>
            </x-slot:toolbar>
            {{-- Filters --}}
            <x-slot:filters>

                <x-cn.crud.filters :action="route('currencies.index')">
                    {{-- filtros personalizados --}}
                </x-cn.crud.filters>

            </x-slot:filters>

            {{-- Tabla principal --}}
            <x-cn.table data-cn-datatable>

                <thead>
                    <tr>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Name
</th>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Code
</th>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Precision
</th>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Symbol
</th>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Decimal Mark
</th>

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     column.stub
========================================================== --}}

<th
    @class([
        '',
        'text-start',
    ])
>
    Thousands Separator
</th>


                        <th class="text-center">
                            Acciones
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($currencies as $currency)

                        <tr>

<td>{{ $currency->name }}</td>
<td>{{ $currency->code }}</td>
<td>{{ $currency->precision }}</td>
<td>{{ $currency->symbol }}</td>
<td>{{ $currency->decimal_mark }}</td>
<td>{{ $currency->thousands_separator }}</td>

                            <td class="text-center">

                                <x-cn.crud.actions>

                                    <x-cn.button.show
                                        :href="route('currencies.show', $currency)" />

                                    <x-cn.button.edit
                                        :href="route('currencies.edit', $currency)" />

                                    <x-cn.crud.confirm
                                        id="delete-currency-{{ $currency->id }}"
                                        form-id="delete-currency-{{ $currency->id }}-form"
                                        title="Eliminar currency"
                                        message="¿Está seguro de eliminar este registro?">

                                        <form
                                            id="delete-currency-{{ $currency->id }}-form"
                                            action="{{ route('currencies.destroy', $currency) }}"
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

                            <td colspan="7">

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
