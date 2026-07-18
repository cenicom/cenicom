<x-layout.app>

    <x-slot:title>
        Ver test_form
    </x-slot>

    <div class="cn-page">

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Detalle de test_form
                    </h1>

                    <p>
                        Consulte la información registrada del elemento.
                    </p>

                </div>

            </div>

        </header>

        <section class="cn-card">

            <div class="cn-card-body">

                <x-cn.group columns="2">

                    

                </x-cn.group>

                <x-cn.actions justify="between">

                    <x-cn.button
                        :href="route('test_forms.edit', $testForm)">

                        Actualizar

                    </x-cn.button>

                    <x-cn.button
                        :href="route('test_forms.index')"
                        variant="secondary">

                        Regresar

                    </x-cn.button>

                </x-cn.actions>

            </div>

        </section>

    </div>

</x-layout.app>
