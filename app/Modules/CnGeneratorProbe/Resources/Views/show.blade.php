<x-layout.app>

    <x-slot:title>
        Ver cn_generator_probe
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'cn_generator_probes', 'url' => null, 'current' => false],
            ['label' => 'Detalle de cn_generator_probe', 'url' => null, 'current' => true],
        ]" />

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Detalle de cn_generator_probe
                    </h1>

                    <p>
                        Consulte la información registrada del elemento.
                    </p>

                </div>

            </div>

        </header>

        <section class="cn-card">

            <div class="cn-card-body">

                <x-cn.forms.group columns="2">

                    

                </x-cn.forms.group>

                <x-cn-form-actions>

                    <x-cn.button
                        :href="route('cn_generator_probes.edit', $cnGeneratorProbe)">

                        Actualizar

                    </x-cn.button>

                    <x-cn.button
                        :href="route('cn_generator_probes.index')"
                        variant="secondary">

                        Regresar

                    </x-cn.button>

                </x-cn-form-actions>

            </div>

        </section>

    </div>

</x-layout.app>
