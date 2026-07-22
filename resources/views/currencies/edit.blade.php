<x-layout.app>
    <x-slot:title>
        currencies
    </x-slot:title>


    <div class="cn-page">


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Editar currency
                    </h1>

                    <p>
                        Editar el elemento en el sistema.
                    </p>

                </div>

            </div>

        </header>


        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn.form id="currency-form" :action="route('currencies.store')" method="POST">


                    @include('currencies._form')

                    {{-- Actions --}}
                    <x-cn.actions>
                        {{-- Guardar --}}
                        <x-cn.button type="submit">
                            Guardar
                        </x-cn.button>
                        {{-- Regresar --}}
                        <x-cn.button :href="route('currencies.index')" variant="secondary">
                            Regresar
                        </x-cn.button>
                    </x-cn.actions>
                </x-cn.form>
            </div>
        </section>
    </div>
</x-layout.app>
