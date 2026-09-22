<x-layout.app>

    <x-slot:title>
        Ver currency
    </x-slot:title>

    <div class="cn-page">

        <x-cn.navigation.breadcrumb :items="[
            ['label' => 'Inicio', 'url' => '/', 'current' => false],
            ['label' => 'currencies', 'url' => null, 'current' => false],
            ['label' => 'Detalle de currency', 'url' => null, 'current' => true],
        ]" />

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div>

                    <h1>
                        Detalle de currency
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
            Name
        </x-cn.forms.label>

        <x-cn.forms.display :value="$currency->name" />

    </x-cn.forms.field>

</div>


{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     field.stub
========================================================== --}}


<div class="col-md-6">

    <x-cn.forms.field>

        <x-cn.forms.label>
            Code
        </x-cn.forms.label>

        <x-cn.forms.display :value="$currency->code" />

    </x-cn.forms.field>

</div>


{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     field.stub
========================================================== --}}


<div class="col-md-6">

    <x-cn.forms.field>

        <x-cn.forms.label>
            Precision
        </x-cn.forms.label>

        <x-cn.forms.display :value="$currency->precision" />

    </x-cn.forms.field>

</div>


{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     field.stub
========================================================== --}}


<div class="col-md-6">

    <x-cn.forms.field>

        <x-cn.forms.label>
            Symbol
        </x-cn.forms.label>

        <x-cn.forms.display :value="$currency->symbol" />

    </x-cn.forms.field>

</div>


{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     field.stub
========================================================== --}}


<div class="col-md-6">

    <x-cn.forms.field>

        <x-cn.forms.label>
            Decimal Mark
        </x-cn.forms.label>

        <x-cn.forms.display :value="$currency->decimal_mark" />

    </x-cn.forms.field>

</div>


{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     field.stub
========================================================== --}}


<div class="col-md-6">

    <x-cn.forms.field>

        <x-cn.forms.label>
            Thousands Separator
        </x-cn.forms.label>

        <x-cn.forms.display :value="$currency->thousands_separator" />

    </x-cn.forms.field>

</div>


                </x-cn.forms.group>

                <x-cn-form-actions>

                    <x-cn.button
                        :href="route('currencies.edit', $currency)">

                        Actualizar

                    </x-cn.button>

                    <x-cn.button
                        :href="route('currencies.index')"
                        variant="secondary">

                        Regresar

                    </x-cn.button>

                </x-cn-form-actions>

            </div>

        </section>

    </div>

</x-layout.app>
