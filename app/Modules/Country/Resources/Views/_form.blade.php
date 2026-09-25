<x-cn.forms.group columns="2">

    {{-- ==========================================================
         CENICOM ERP
         CN Generator - Component Stub
         input.stub
    ========================================================== --}}

    <div class="col-md-6">
        <x-cn.forms.input
            name="name"
            type="text"
            label="Nombre"
            placeholder="Ingrese el nombre del país"
            :value="$country->name ?? null"
            :required="true"
            :readonly="false"
            :maxlength="null"
            :step="null"
        />
    </div>

    {{-- ==========================================================
         CENICOM ERP
         CN Generator - Component Stub
         input.stub
    ========================================================== --}}

    <div class="col-md-6">
        <x-cn.forms.input
            name="iso2"
            type="text"
            label="Código ISO 2"
            placeholder="Ingrese el código ISO de 2 letras"
            :value="$country->iso2 ?? null"
            :required="true"
            :readonly="false"
            :maxlength="2"
            :step="null"
        />
    </div>

    {{-- ==========================================================
         CENICOM ERP
         CN Generator - Component Stub
         input.stub
    ========================================================== --}}

    <div class="col-md-6">
        <x-cn.forms.input
            name="iso3"
            type="text"
            label="Código ISO 3"
            placeholder="Ingrese el código ISO de 3 letras"
            :value="$country->iso3 ?? null"
            :required="true"
            :readonly="false"
            :maxlength="3"
            :step="null"
        />
    </div>

</x-cn.forms.group>
