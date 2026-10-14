<x-layout.app>
    <x-cn.navigation.breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('dashboard')],
        ['label' => 'Sedes educativas', 'url' => route('campuses.index')],
        ['label' => $campus->name, 'url' => route('campuses.show', $campus)],
        ['label' => 'Editar sede', 'current' => true],
    ]" />

    <x-cn.crud
        title="Editar sede educativa"
        subtitle="Actualización de los datos permitidos de la sede"
        icon="bi bi-building"
    >
        <form method="POST" action="{{ route('campuses.update', $campus) }}">
            @csrf
            @method('PUT')

            <div class="cn-group">
                <div class="cn-group-2">
                    <div class="cn-field">
                        <x-cn.forms.label for="institutionName" value="Institución" />
                        <x-cn.forms.input
                            id="institutionName"
                            name="institutionName"
                            type="text"
                            :value="$campus->institution?->name ?? 'No disponible'"
                            readonly
                        />
                        <x-cn.forms.help>
                            La institución no se puede cambiar desde este formulario.
                        </x-cn.forms.help>
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="campusCode" value="Código de la sede" />
                        <x-cn.forms.input
                            id="campusCode"
                            name="campusCode"
                            type="text"
                            :value="$campus->code"
                            readonly
                        />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="shortCode" value="Código corto" />
                        <x-cn.forms.input
                            id="shortCode"
                            name="shortCode"
                            type="text"
                            :value="$campus->short_code"
                            readonly
                        />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="name" value="Nombre de la sede" required />
                        <x-cn.forms.input
                            id="name"
                            name="name"
                            type="text"
                            :value="old('name', $campus->name)"
                            required
                            maxlength="255"
                        />
                        <x-cn.forms.error for="name" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="ministryCode" value="Código ministerial" />
                        <x-cn.forms.input
                            id="ministryCode"
                            name="ministryCode"
                            type="text"
                            :value="old('ministryCode', $campus->ministry_code)"
                            maxlength="50"
                        />
                        <x-cn.forms.error for="ministryCode" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="campusStatus" value="Estado" />
                        <x-cn.forms.input
                            id="campusStatus"
                            name="campusStatus"
                            type="text"
                            :value="$campus->status"
                            readonly
                        />
                        <x-cn.forms.help>
                            El estado se gestiona mediante su propio ciclo de vida.
                        </x-cn.forms.help>
                    </div>
                </div>
            </div>

            <fieldset class="cn-field mt-4">
                <legend class="cn-label">Dirección de la sede</legend>

                <div class="cn-group-2">
                    <div class="cn-field">
                        <x-cn.forms.label for="address_country_id" value="País" required />

                        <select
                            id="address_country_id"
                            name="address[country_id]"
                            class="form-select @error('address.country_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Cargando países…</option>
                        </select>

                        <x-cn.forms.error for="address.country_id" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_state_id" value="Departamento o estado" required />

                        <select
                            id="address_state_id"
                            name="address[state_id]"
                            class="form-select @error('address.state_id') is-invalid @enderror"
                            required
                            disabled
                        >
                            <option value="">Seleccione primero un país</option>
                        </select>

                        <x-cn.forms.error for="address.state_id" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_city_id" value="Ciudad o municipio" required />

                        <select
                            id="address_city_id"
                            name="address[city_id]"
                            class="form-select @error('address.city_id') is-invalid @enderror"
                            required
                            disabled
                        >
                            <option value="">Seleccione primero un departamento o estado</option>
                        </select>

                        <x-cn.forms.error for="address.city_id" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_neighborhood" value="Barrio" />
                        <x-cn.forms.input
                            id="address_neighborhood"
                            name="address[neighborhood]"
                            type="text"
                            :value="old('address.neighborhood', $address->neighborhood)"
                            maxlength="255"
                        />
                        <x-cn.forms.error for="address.neighborhood" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_address" value="Dirección" />
                        <x-cn.forms.input
                            id="address_address"
                            name="address[address]"
                            type="text"
                            :value="old('address.address', $address->address)"
                            maxlength="255"
                            autocomplete="street-address"
                        />
                        <x-cn.forms.error for="address.address" />
                    </div>
                </div>
            </fieldset>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <x-cn.button.save type="submit">
                    Guardar cambios
                </x-cn.button.save>

                <x-cn.button.cancel href="{{ route('campuses.show', $campus) }}">
                    Cancelar
                </x-cn.button.cancel>
            </div>
        </form>
    </x-cn.crud>
</x-layout.app>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const countrySelect = document.getElementById('address_country_id');
        const stateSelect = document.getElementById('address_state_id');
        const citySelect = document.getElementById('address_city_id');

        const selectedCountry = @json(old('address.country_id', $address->country_id));
        const selectedState = @json(old('address.state_id', $address->state_id));
        const selectedCity = @json(old('address.city_id', $address->city_id));

        function resetSelect(select, placeholder) {
            select.replaceChildren(new Option(placeholder, ''));
            select.value = '';
            select.disabled = true;
        }

        async function loadOptions(select, url, placeholder, selected = '') {
            resetSelect(select, 'Cargando…');

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP ${response.status}`);
                }

                const payload = await response.json();

                if (!Array.isArray(payload.data)) {
                    throw new Error('Formato JSON geográfico inesperado');
                }

                resetSelect(select, placeholder);

                for (const option of payload.data) {
                    select.add(new Option(option.name, option.id));
                }

                select.disabled = false;

                if (selected !== '' && selected !== null) {
                    select.value = String(selected);
                }

                return select.value !== '';
            } catch (error) {
                resetSelect(select, 'No fue posible cargar las opciones');
                console.error('Carga geográfica fallida:', error);
                return false;
            }
        }

        countrySelect.addEventListener('change', async () => {
            resetSelect(stateSelect, 'Seleccione primero un país');
            resetSelect(citySelect, 'Seleccione primero un departamento o estado');

            if (!countrySelect.value) {
                return;
            }

            await loadOptions(
                stateSelect,
                `/countries/${encodeURIComponent(countrySelect.value)}/states`,
                'Seleccione un departamento o estado',
            );
        });

        stateSelect.addEventListener('change', async () => {
            resetSelect(citySelect, 'Seleccione primero un departamento o estado');

            if (!stateSelect.value) {
                return;
            }

            await loadOptions(
                citySelect,
                `/states/${encodeURIComponent(stateSelect.value)}/cities`,
                'Seleccione una ciudad o municipio',
            );
        });

        (async () => {
            const countryLoaded = await loadOptions(
                countrySelect,
                @json(route('geographic.countries')),
                'Seleccione un país',
                selectedCountry,
            );

            if (!countryLoaded) {
                return;
            }

            const stateLoaded = await loadOptions(
                stateSelect,
                `/countries/${encodeURIComponent(countrySelect.value)}/states`,
                'Seleccione un departamento o estado',
                selectedState,
            );

            if (!stateLoaded) {
                return;
            }

            await loadOptions(
                citySelect,
                `/states/${encodeURIComponent(stateSelect.value)}/cities`,
                'Seleccione una ciudad o municipio',
                selectedCity,
            );
        })();
    });
</script>
