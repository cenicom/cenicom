<x-cn.forms.group columns="2">

    

{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     input.stub
========================================================== --}}

<div class="col-md-6">
    <x-cn.input
        name="name"
        type="string"
        label="Name"
        placeholder="Enter Name"
        :value="NULL"
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
    <x-cn.input
        name="code"
        type="string"
        label="Code"
        placeholder="Enter Code"
        :value="NULL"
        :required="true"
        :readonly="false"
        :maxlength="3"
        :step="null"
    />
</div>




{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     number.stub
========================================================== --}}

<div class="col-md-6">
    <x-cn.number
        name="precision"
        label="Precision"
        :value="2"
        :required="true"
        :readonly="false"
        :step="null"
    />
</div>




{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     input.stub
========================================================== --}}

<div class="col-md-6">
    <x-cn.input
        name="symbol"
        type="string"
        label="Symbol"
        placeholder="Enter Symbol"
        :value="NULL"
        :required="true"
        :readonly="false"
        :maxlength="10"
        :step="null"
    />
</div>




{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     input.stub
========================================================== --}}

<div class="col-md-6">
    <x-cn.input
        name="decimal_mark"
        type="string"
        label="Decimal Mark"
        placeholder="Enter Decimal Mark"
        :value="'.'"
        :required="true"
        :readonly="false"
        :maxlength="1"
        :step="null"
    />
</div>




{{-- ==========================================================
     CENICOM ERP
     CN Generator - Component Stub
     input.stub
========================================================== --}}

<div class="col-md-6">
    <x-cn.input
        name="thousands_separator"
        type="string"
        label="Thousands Separator"
        placeholder="Enter Thousands Separator"
        :value="','"
        :required="true"
        :readonly="false"
        :maxlength="1"
        :step="null"
    />
</div>


</x-cn.forms.group>
