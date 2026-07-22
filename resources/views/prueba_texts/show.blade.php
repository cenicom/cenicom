<x-layout.app>

    <x-slot:title>
        Ver prueba_text
    </x-slot>

    <div class="cn-page">

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Detalle de prueba_text
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
                        :href="route('prueba_texts.edit', $pruebaText)">

                        Actualizar

                    </x-cn.button>

                    <x-cn.button
                        :href="route('prueba_texts.index')"
                        variant="secondary">

                        Regresar

                    </x-cn.button>

                </x-cn.actions>

            </div>

        </section>

    </div>

</x-layout.app>
