<x-cn.forms.group columns="2">

    {{-- Nombre de la institución --}}
    <x-cn.forms.field id="institution-name-field">

        <x-cn.forms.label
            for="institution-name"
            required
        >
            Nombre
        </x-cn.forms.label>

        <x-cn.forms.input
            id="institution-name"
            name="name"
            type="text"
            :value="old('name', $institution->name ?? '')"
            placeholder="Ingrese el nombre oficial"
            required
            maxlength="255"
        />

        <x-cn.forms.help id="institution-name-help">
            Nombre oficial de la institución educativa.
        </x-cn.forms.help>

        <x-cn.forms.error
            for="name"
        />

    </x-cn.forms.field>

    {{-- Registro oficial: país --}}
    <x-cn.forms.field id="institution-registration-country-field">

        <x-cn.forms.label
            for="official-registration-country"
            required
        >
            País del registro oficial
        </x-cn.forms.label>

        <x-cn.forms.input
            id="official-registration-country"
            name="officialRegistration[country]"
            type="text"
            :value="old(
                'officialRegistration.country',
                $institution->official_registration_country ?? ''
            )"
            placeholder="Código de país"
            required
            maxlength="2"
        />

        <x-cn.forms.help id="official-registration-country-help">
            Código de país asociado al registro oficial.
        </x-cn.forms.help>

        <x-cn.forms.error
            for="officialRegistration.country"
        />

    </x-cn.forms.field>

    {{-- Registro oficial: autoridad --}}
    <x-cn.forms.field id="institution-registration-authority-field">

        <x-cn.forms.label
            for="official-registration-authority"
            required
        >
            Autoridad del registro oficial
        </x-cn.forms.label>

        <x-cn.forms.input
            id="official-registration-authority"
            name="officialRegistration[authority]"
            type="text"
            :value="old(
                'officialRegistration.authority',
                $institution->official_registration_authority ?? ''
            )"
            placeholder="Ingrese la autoridad"
            required
            maxlength="150"
        />

        <x-cn.forms.help id="official-registration-authority-help">
            Autoridad que emite o registra la institución.
        </x-cn.forms.help>

        <x-cn.forms.error
            for="officialRegistration.authority"
        />

    </x-cn.forms.field>

    {{-- Registro oficial: valor --}}
    <x-cn.forms.field id="institution-registration-value-field">

        <x-cn.forms.label
            for="official-registration-value"
            required
        >
            Valor del registro oficial
        </x-cn.forms.label>

        <x-cn.forms.input
            id="official-registration-value"
            name="officialRegistration[value]"
            type="text"
            :value="old(
                'officialRegistration.value',
                $institution->official_registration_value ?? ''
            )"
            placeholder="Ingrese el número o valor del registro"
            required
            maxlength="100"
        />

        <x-cn.forms.help id="official-registration-value-help">
            Número o valor oficial asignado por la autoridad.
        </x-cn.forms.help>

        <x-cn.forms.error
            for="officialRegistration.value"
        />

    </x-cn.forms.field>

</x-cn.forms.group>

