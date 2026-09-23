<x-layout.app>
    <x-slot:title>
        cn_generator_probes
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'cn_generator_probes', 'url' => null, 'current' => false],
            ['label' => 'Crear cn_generator_probe', 'url' => null, 'current' => true],
        ]" />


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Crear cn_generator_probe
                    </h1>

                    <p>
                        Registre un nuevo elemento en el sistema.
                    </p>

                </div>

            </div>

        </header>


        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn.forms.form id="cn_generator_probe-form" :action="route('cn_generator_probes.store')" method="POST">


                    @include('cn_generator_probes::_form')

                    {{-- Actions --}}
                    <x-cn-form-actions>
                        {{-- Guardar --}}
                        <x-cn.button type="submit">
                            Guardar
                        </x-cn.button>
                        {{-- Regresar --}}
                        <x-cn.button :href="route('cn_generator_probes.index')" variant="secondary">
                            Regresar
                        </x-cn.button>
                    </x-cn-form-actions>
                </x-cn.forms.form>
            </div>
        </section>
    </div>
</x-layout.app>
