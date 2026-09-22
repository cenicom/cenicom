<x-layout.app>
    <x-slot:title>
        states
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'states', 'url' => null, 'current' => false],
            ['label' => 'Crear state', 'url' => null, 'current' => true],
        ]" />


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Crear state
                    </h1>

                    <p>
                        Registre un nuevo elemento en el sistema.
                    </p>

                </div>

            </div>

        </header>


        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn.forms.form id="state-form" :action="route('states.store')" method="POST">


                    @include('states::_form')

                    {{-- Actions --}}
                    <x-cn-form-actions>
                        {{-- Guardar --}}
                        <x-cn.button type="submit">
                            Guardar
                        </x-cn.button>
                        {{-- Regresar --}}
                        <x-cn.button :href="route('states.index')" variant="secondary">
                            Regresar
                        </x-cn.button>
                    </x-cn-form-actions>
                </x-cn.forms.form>
            </div>
        </section>
    </div>
</x-layout.app>
