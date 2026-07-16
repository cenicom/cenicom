<x-layout.app>
    <x-slot:title>
        institution_tests
    </x-slot:title>


    <div class="cn-page">


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Crear institution_test
                    </h1>

                    <p>
                        Registre un nuevo elemento en el sistema.
                    </p>

                </div>

            </div>

        </header>


        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn.form id="institution_tests -form" :action="route(' institution_tests .store')" method="POST">


                    @include('institution_tests ._form')

                    {{-- Actions --}}
                    <x-cn.actions>
                        {{-- Guardar --}}
                        <x-cn.button type="submit">
                            Guardar
                        </x-cn.button>
                        {{-- Regresar --}}
                        <x-cn.button :href="route('institution_tests .index')" variant="secondary">
                            Regresar
                        </x-cn.button>
                    </x-cn.actions>
                </x-cn.form>
            </div>
        </section>
    </div>
</x-layout.app>
