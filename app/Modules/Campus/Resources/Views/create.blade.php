<x-layout.app>
    <x-cn.navigation.breadcrumb :items="[
        [
            'label' => 'Inicio',
            'url' => route('dashboard'),
        ],
        [
            'label' => 'Sedes educativas',
            'url' => route('campuses.index'),
        ],
        [
            'label' => 'Crear sede',
            'current' => true,
        ],
    ]" />
    <x-cn.crud title="Crear sede educativa" subtitle="Registro de una sede perteneciente a una institución educativa"
        icon="bi bi-building">
        <form method="POST" action="{{ route('campuses.store') }}">
            @csrf

            <div class="cn-group">
                <div class="cn-group-2">
                    <div class="cn-field">
                        <x-cn.forms.label for="institutionId" value="Institución" required />

                        <select id="institutionId" name="institutionId"
                            class="form-select @error('institutionId') is-invalid @enderror" required>
                            <option value="">Seleccione una institución</option>
                            @foreach ($institutions as $institution)
                                <option value="{{ $institution->id }}" @selected(old('institutionId') == $institution->id)>
                                    {{ $institution->name }} ({{ $institution->short_code }})
                                </option>
                            @endforeach
                        </select>

                        <x-cn.forms.error for="institutionId" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="shortCode" value="Código corto de la sede" required />

                        <x-cn.forms.input id="shortCode" name="shortCode" type="text" :value="old('shortCode')"
                            placeholder="Ej. SEDEPR" required minlength="6" maxlength="15" pattern="[A-Z]{6,15}" />

                        <x-cn.forms.help id="campus-short-code-help">
                            Letras mayúsculas, entre 6 y 15 caracteres.
                        </x-cn.forms.help>

                        <x-cn.forms.error for="shortCode" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="name" value="Nombre de la sede" required />

                        <x-cn.forms.input id="name" name="name" type="text" :value="old('name')" required
                            maxlength="255" />

                        <x-cn.forms.error for="name" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="ministryCode" value="Código ministerial" />

                        <x-cn.forms.input id="ministryCode" name="ministryCode" type="text" :value="old('ministryCode')"
                            maxlength="50" />

                        <x-cn.forms.error for="ministryCode" />
                    </div>
                </div>
            </div>

            <fieldset class="cn-field mt-4">
                <legend class="cn-label">Dirección de la sede</legend>

                <div class="cn-group-2">
                    <div class="cn-field">
                        <x-cn.forms.label for="address_country_id" value="País" required />

                        <select id="address_country_id" name="address[country_id]"
                            class="form-select @error('address.country_id') is-invalid @enderror" required>
                            <option value="">Cargando países…</option>
                        </select>

                        <x-cn.forms.error for="address.country_id" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_state_id" value="Departamento o estado" required />

                        <select id="address_state_id" name="address[state_id]"
                            class="form-select @error('address.state_id') is-invalid @enderror" required disabled>
                            <option value="">Seleccione primero un país</option>
                        </select>

                        <x-cn.forms.error for="address.state_id" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_city_id" value="Ciudad o municipio" required />

                        <select id="address_city_id" name="address[city_id]"
                            class="form-select @error('address.city_id') is-invalid @enderror" required disabled>
                            <option value="">Seleccione primero un departamento o estado</option>
                        </select>

                        <x-cn.forms.error for="address.city_id" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_neighborhood" value="Barrio" />

                        <x-cn.forms.input id="address_neighborhood" name="address[neighborhood]" type="text"
                            :value="old('address.neighborhood')" maxlength="255" />

                        <x-cn.forms.error for="address.neighborhood" />
                    </div>

                    <div class="cn-field">
                        <x-cn.forms.label for="address_address" value="Dirección" />

                        <x-cn.forms.input id="address_address" name="address[address]" type="text"
                            :value="old('address.address')" maxlength="255" autocomplete="street-address" />

                        <x-cn.forms.error for="address.address" />
                    </div>
                </div>
            </fieldset>

            <div class="mt-4 d-flex gap-2">
                <x-cn.button.save type="submit">
                    Crear sede
                </x-cn.button.save>

                <x-cn.button.cancel href="{{ route('campuses.index') }}">
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

        const oldCountry = @json(old('address.country_id', ''));
        const oldState = @json(old('address.state_id', ''));
        const oldCity = @json(old('address.city_id', ''));

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
                    const item = new Option(option.name, option.id);
                    select.add(item);
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
                oldCountry,
            );

            if (!countryLoaded) {
                return;
            }

            const stateLoaded = await loadOptions(
                stateSelect,
                `/countries/${encodeURIComponent(countrySelect.value)}/states`,
                'Seleccione un departamento o estado',
                oldState,
            );

            if (!stateLoaded) {
                return;
            }

            await loadOptions(
                citySelect,
                `/states/${encodeURIComponent(stateSelect.value)}/cities`,
                'Seleccione una ciudad o municipio',
                oldCity,
            );
        })();
    });
</script>
