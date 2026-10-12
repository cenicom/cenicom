<div class="cn-group">
    <div class="cn-group-2">

        {{-- Nombre de la institución --}}
        <div class="cn-field">
            <x-cn.forms.label
                for="name"
                value="Nombre de la institución"
                required
            />

            <x-cn.forms.input
                id="name"
                name="name"
                type="text"
                :value="old('name', $institution->name ?? '')"
                required
                maxlength="255"
                autocomplete="organization"
            />

            <x-cn.forms.error for="name" />
        </div>

        {{-- Registro oficial --}}
        <fieldset class="cn-field">
            <legend class="cn-label">
                Registro oficial
            </legend>

            <div class="cn-group-2">

                {{-- País --}}
                <div class="cn-field">
                    <x-cn.forms.label
                        for="officialRegistration_country"
                        value="País del registro oficial"
                        :required="!isset($institution)"
                    />

                    <x-cn.forms.input
                        id="officialRegistration_country"
                        name="officialRegistration[country]"
                        type="text"
                        :value="old(
                            'officialRegistration.country',
                            $institution->official_registration_country ?? ''
                        )"
                        maxlength="2"
                        autocomplete="off"
                        :required="!isset($institution)"
                    />

                    <x-cn.forms.error for="officialRegistration.country" />
                    <p class="cn-help">Código de país de dos caracteres.</p>
                </div>

                {{-- Autoridad --}}
                <div class="cn-field">
                    <x-cn.forms.label
                        for="officialRegistration_authority"
                        value="Autoridad de registro"
                        :required="!isset($institution)"
                    />

                    <x-cn.forms.input
                        id="officialRegistration_authority"
                        name="officialRegistration[authority]"
                        type="text"
                        :value="old(
                            'officialRegistration.authority',
                            $institution->official_registration_authority ?? ''
                        )"
                        maxlength="150"
                        autocomplete="off"
                        :required="!isset($institution)"
                    />

                    <x-cn.forms.error for="officialRegistration.authority" />
                </div>

                {{-- Valor del registro --}}
                <div class="cn-field">
                    <x-cn.forms.label
                        for="officialRegistration_value"
                        value="Número o valor del registro"
                        :required="!isset($institution)"
                    />

                    <x-cn.forms.input
                        id="officialRegistration_value"
                        name="officialRegistration[value]"
                        type="text"
                        :value="old(
                            'officialRegistration.value',
                            $institution->official_registration_value ?? ''
                        )"
                        maxlength="100"
                        autocomplete="off"
                        :required="!isset($institution)"
                    />

                    <x-cn.forms.error for="officialRegistration.value" />
                </div>

            </div>
        </fieldset>

    </div>
</div>
