<x-layout.app>
    <x-slot:title>
        generator_probes
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'generator_probes', 'url' => null, 'current' => false],
            ['label' => 'Editar generator_probe', 'url' => null, 'current' => true],
        ]" />


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Editar generator_probe
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
    id="generator_probe-form"
    :action="route('generator_probes.update', $generatorProbe)"
    method="PUT"
>
    @include('generator_probes::_form')

    <x-cn-form-actions>
        <x-cn.button type="submit">
            Guardar
        </x-cn.button>

        <x-cn.button
            :href="route('generator_probes.index')"
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
