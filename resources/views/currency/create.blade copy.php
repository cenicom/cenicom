<x-layout.app>

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
                    <h1>Nueva Moneda</h1>

                    <p>
                        Registre una nueva moneda en el sistema.
                    </p>
                </div>

            </div>

        </header>

        <section class="cn-card">

            <div class="cn-card-body">

                <x-cn.form
                    id="currency-form"
                    :action="route('currencies.store')"
                    method="POST"
                >

                    @include('currencies._form')

                    <x-cn.actions>

                        <x-cn.button type="submit">

                            <i class="fas fa-save"></i>

                            Guardar

                        </x-cn.button>

                        <x-cn.button
                            :href="route('currencies.index')"
                            variant="secondary"
                            class="js-confirm-back"
                        >

                            <i class="fas fa-arrow-left"></i>

                            Regresar

                        </x-cn.button>

                    </x-cn.actions>

                </x-cn.form>

            </div>

        </section>

    </div>

    @push('scripts')

        <script>

            document
                .getElementById('currency-form')
                ?.addEventListener('submit', function (event) {

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

</x-layout.app>
