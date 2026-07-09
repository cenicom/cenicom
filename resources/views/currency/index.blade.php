<x-layout.app>

```
<x-slot name="title">
    Monedas
</x-slot>

<div class="cn-page">

    <header class="cn-page-header">

        <div class="cn-page-title">

            <div class="cn-page-icon">
                <i class="fas fa-coins"></i>
            </div>

            <div>
                <h1>
                    Monedas
                </h1>

                <p>
                    Administración y configuración de monedas del sistema.
                </p>
            </div>

        </div>


        <div class="cn-page-actions">

            <a href="{{ route('currencies.create') }}"
               class="cn-btn cn-btn-primary">

                <i class="fas fa-plus"></i>
                Nueva moneda

            </a>

        </div>

    </header>


    <section class="cn-card">

        <div class="cn-card-body">

            <div class="table-responsive">

                <table id="currency-table"
                       class="table cn-table">

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

                                @if($currency->status)

                                    <span class="badge bg-success">
                                        Activa
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactiva
                                    </span>

                                @endif

                            </td>


                            <td class="text-center">

                                <a href="{{ route('currencies.show', $currency) }}"
                                   class="btn btn-sm btn-info"
                                   title="Ver detalle">

                                    <i class="fas fa-eye"></i>

                                </a>


                                <a href="{{ route('currencies.edit', $currency) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Editar">

                                    <i class="fas fa-edit"></i>

                                </a>


                                <form action="{{ route('currencies.destroy', $currency) }}"
                                      method="POST"
                                      class="d-inline js-confirm-delete">

                                    @csrf

                                    @method('DELETE')


                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Eliminar">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center">

                                No existen monedas registradas.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            <div class="cn-pagination mt-3">

                {{ $currencies->links() }}

            </div>


        </div>

    </section>

</div>


@push('scripts')

    <script>

        document.querySelectorAll('.js-confirm-delete')
            .forEach(form => {

                form.addEventListener('submit', function(event) {

                    event.preventDefault();


                    Swal.fire({

                        title: '¿Eliminar moneda?',

                        text: 'Esta acción modificará la información del sistema.',

                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText: 'Sí, eliminar',

                        cancelButtonText: 'Cancelar'

                    }).then((result) => {

                        if (result.isConfirmed) {

                            form.submit();

                        }

                    });

                });

            });

    </script>

@endpush
```

</x-layout.app>
