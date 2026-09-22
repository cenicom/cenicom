<x-layout.app>
    <x-slot:title>
        currencies
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'currencies', 'url' => null, 'current' => false],
            ['label' => 'Editar currency', 'url' => null, 'current' => true],
        ]" />


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


<x-cn.forms.form
    id="currency-form"
    :action="route('currencies.update', $currency)"
    method="PUT"
>
    @include('currencies::_form')

    <x-cn-form-actions>
        <x-cn.button type="submit">
            Guardar
        </x-cn.button>

        <x-cn.button
            :href="route('currencies.index')"
            variant="secondary"
        >
            Regresar
        </x-cn.button>
    </x-cn-form-actions>
</x-cn.forms.form>
            </div>
        </section>
    </div>
</x-layout.app>
