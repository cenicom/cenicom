<x-layout.app>
    <x-slot:title>
        cities
    </x-slot:title>


    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'cities', 'url' => null, 'current' => false],
            ['label' => 'Crear city', 'url' => null, 'current' => true],
        ]" />


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Crear city
                    </h1>

                    <p>
                        Registre un nuevo elemento en el sistema.
                    </p>

                </div>

            </div>

        </header>


        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn.forms.form id="city-form" :action="route('cities.store')" method="POST">


                    @include('cities._form')

                    {{-- Actions --}}
                    <x-cn-form-actions>
                        {{-- Guardar --}}
                        <x-cn.button type="submit">
                            Guardar
                        </x-cn.button>
                        {{-- Regresar --}}
                        <x-cn.button :href="route('cities.index')" variant="secondary">
                            Regresar
                        </x-cn.button>
                    </x-cn-form-actions>
                </x-cn.forms.form>
            </div>
        </section>
    </div>
</x-layout.app>
