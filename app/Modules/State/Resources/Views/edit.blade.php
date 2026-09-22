<x-layout.app>
    <x-slot:title>
        states
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'states', 'url' => null, 'current' => false],
            ['label' => 'Editar state', 'url' => null, 'current' => true],
        ]" />


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Editar state
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
    id="state-form"
    :action="route('states.update', $state)"
    method="PUT"
>
    @include('states::_form')

    <x-cn-form-actions>
        <x-cn.button type="submit">
            Guardar
        </x-cn.button>

        <x-cn.button
            :href="route('states.index')"
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
