<x-layout.app>

    <x-slot name="title">
        Editar Moneda
    </x-slot>

    <div class="cn-page">

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div class="cn-page-icon">
                    <i class="fas fa-coins"></i>
                </div>

                <div>

                    <h1>Editar Moneda</h1>

                    <p>
                        Actualice la información de la moneda.
                    </p>

                </div>

            </div>

        </header>

        <section class="cn-card">

            <div class="cn-card-body">

                <x-cn.form id="currency-form" :action="route('currencies.update', $currency)" method="PUT">

                    @include('currencies._form')

                    <x-cn.actions>

                        <x-cn.button type="submit">

                            <i class="fas fa-save"></i>

                            Actualizar

                        </x-cn.button>

                        <x-cn.button :href="route('currencies.index')" variant="secondary" class="js-confirm-back">

                            <i class="fas fa-arrow-left"></i>

                            Regresar

                        </x-cn.button>

                    </x-cn.actions>

                </x-cn.form>

            </div>

        </section>

    </div>

</x-layout.app>
