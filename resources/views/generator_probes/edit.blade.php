<x-layout.app>
    <x-slot:title>
        generator_probes
    </x-slot:title>


    <div class="cn-page">


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


                <x-cn.form
    id="generator_probe-form"
    :action="route('generator_probes.update', $generatorProbe)"
    method="POST"
>
    @csrf
    @method('PUT')

    @include('generator_probes._form')

    {{-- Actions --}}
    <x-cn.actions>
        <x-cn.button type="submit">
            Guardar
        </x-cn.button>

        <x-cn.button
            :href="route('generator_probes.index')"
            variant="secondary"
        >
            Regresar
        </x-cn.button>
    </x-cn.actions>
</x-cn.form>
            </div>
        </section>
    </div>
</x-layout.app>
