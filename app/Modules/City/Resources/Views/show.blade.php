<x-layout.app>

    <x-slot:title>
        Ver city
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'cities', 'url' => null, 'current' => false],
            ['label' => 'Detalle de city', 'url' => null, 'current' => true],
        ]" />

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Detalle de city
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

                    {{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     field.stub
========================================================== --}}


<div class="col-md-6">

    <x-cn.forms.field>

        <x-cn.forms.label>
            State Id
        </x-cn.forms.label>

        <x-cn.forms.display :value="$city->state_id" />

    </x-cn.forms.field>

</div>


                </x-cn.forms.group>

                <x-cn-form-actions>

                    <x-cn.button
                        :href="route('cities.edit', $city)">

                        Actualizar

                    </x-cn.button>

                    <x-cn.button
                        :href="route('cities.index')"
                        variant="secondary">

                        Regresar

                    </x-cn.button>

                </x-cn-form-actions>

            </div>

        </section>

    </div>

</x-layout.app>
