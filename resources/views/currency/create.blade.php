<x-layout.app>

    ```
    <x-slot name="title">
        Crear Moneda
    </x-slot>


    <div class="cn-page">


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div class="cn-page-icon">

                    <i class="fas fa-coins"></i>

                </div>


                <div>

                    <h1>
                        Nueva Moneda
                    </h1>

                    <p>
                        Registre una nueva moneda en el sistema.
                    </p>

                </div>

            </div>

        </header>



        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn-form action="{{ route('currencies.store') }}" method="POST">

                    <x-cn-row>
                        {{-- code --}}
                        <x-cn-col md="6">
                            <x-cn-field label="Código" required>
                                <x-cn-input name="code" value="{{ old('code') }}" maxlength="10" />
                            </x-cn-field>
                        </x-cn-col>
                        {{-- name --}}
                        <x-cn-col md="6">
                            <x-cn-field label="Nombre" required>
                                <x-cn-input name="name" value="{{ old('name') }}" />
                            </x-cn-field>
                        </x-cn-col>
                        {{-- symbol --}}
                        <x-cn-field label="Símbolo" help="Ejemplo: $, €, ¥">
                            <x-cn-input name="symbol" maxlength="5" />
                        </x-cn-field>




                        <div class="col-md-6 mb-3">


                            <label class="form-label">

                                Estado

                            </label>


                            <select name="status" class="form-select">


                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>

                                    Activa

                                </option>


                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>

                                    Inactiva

                                </option>


                            </select>


                        </div>


                    </x-cn-row>



                    <x-cn-form-actions>
                        <x-cn-button type="submit">
                            <i class="fas fa-save"></i>
                            Guardar
                        </x-cn-button>

                        <a href="{{ route('currencies.index') }}" class="cn-btn cn-btn-secondary js-confirm-back">
                            <i class="fas fa-arrow-left"></i>
                            Regresar
                        </a>
                    </x-cn-form-actions>



                </x-cn-form>


            </div>


        </section>


    </div>



    @push('scripts')
        <script>
            document
                .getElementById('currency-form')
                .addEventListener('submit', function(event) {


                    event.preventDefault();


                    Swal.fire({

                        title: '¿Guardar moneda?',

                        text: 'Revise la información antes de registrar.',

                        icon: 'question',

                        showCancelButton: true,

                        confirmButtonText: 'Sí, guardar',

                        cancelButtonText: 'Revisar nuevamente'


                    }).then((result) => {


                        if (result.isConfirmed) {

                            this.submit();

                        }


                    });


                });
        </script>
    @endpush
    ```

</x-layout.app>
