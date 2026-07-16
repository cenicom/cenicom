<x-layout.app>
    <x-slot:title>
        test_modules
    </x-slot:title>


    <div class="cn-page">


        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Crear test_module
                    </h1>

                    <p>
                        Registre un nuevo elemento en el sistema.
                    </p>

                </div>

            </div>

        </header>


        <section class="cn-card">


            <div class="cn-card-body">


                <x-cn.form id="test_modules -form" :action="route(' test_modules .store')" method="POST">


                    @include('test_modules ._form')

                    {{-- Actions --}}
                    <x-cn.actions>
                        {{-- Guardar --}}
                        <x-cn.button type="submit">
                            Guardar
                        </x-cn.button>
                        {{-- Regresar --}}
                        <x-cn.button :href="route('test_modules .index')" variant="secondary">
                            Regresar
                        </x-cn.button>
                    </x-cn.actions>
                </x-cn.form>
            </div>
        </section>
    </div>
</x-layout.app>
