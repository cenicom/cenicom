<x-layout.app>
    <x-slot:title>
        countries
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'countries', 'url' => null, 'current' => false],
            ['label' => 'Editar country', 'url' => null, 'current' => true],
        ]" />


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Editar country
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
    id="country-form"
    :action="route('countries.update', $country)"
    method="PUT"
>
    @include('countries._form')

    <x-cn-form-actions>
        <x-cn.button type="submit">
            Guardar
        </x-cn.button>

        <x-cn.button
            :href="route('countries.index')"
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
