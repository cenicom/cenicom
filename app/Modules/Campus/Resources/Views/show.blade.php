<x-layout.app>
    <x-cn.navigation.breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('dashboard')],
        ['label' => 'Sedes educativas', 'url' => route('campuses.index')],
        ['label' => 'Consultar sede', 'current' => true],
    ]" />

    <x-cn.crud
        title="Detalle de la sede"
        subtitle="Información registrada de la sede educativa"
        icon="bi bi-building"
    >
        <x-slot:actions>
            @if ($permissions['update'])
                <a
                    href="{{ route('campuses.edit', $campus) }}"
                    class="btn btn-outline-primary"
                >
                    Editar sede
                </a>
            @endif
        </x-slot:actions>

        <div class="cn-group">
            <div class="cn-group-2">
                <div class="cn-field">
                    <span class="cn-label">Institución</span>
                    <p>{{ $campus->institution?->name ?? 'No disponible' }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Código de la sede</span>
                    <p>{{ $campus->code }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Código corto</span>
                    <p>{{ $campus->short_code }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Nombre</span>
                    <p>{{ $campus->name }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Código ministerial</span>
                    <p>{{ $campus->ministry_code ?: 'No registrado' }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Estado</span>
                    <p>{{ $campus->status }}</p>
                </div>
            </div>
        </div>

        <fieldset class="cn-field mt-4">
            <legend class="cn-label">Dirección de la sede</legend>

            <div class="cn-group-2">
                <div class="cn-field">
                    <span class="cn-label">País</span>
                    <p>{{ $countryName }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Departamento o estado</span>
                    <p>{{ $stateName }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Ciudad o municipio</span>
                    <p>{{ $cityName }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Barrio</span>
                    <p>{{ $address->neighborhood ?: 'No registrado' }}</p>
                </div>

                <div class="cn-field">
                    <span class="cn-label">Dirección</span>
                    <p>{{ $address->address ?: 'No registrada' }}</p>
                </div>
            </div>
        </fieldset>

        <div class="mt-4">
            <x-cn.button.cancel href="{{ route('campuses.index') }}">
                Volver al listado
            </x-cn.button.cancel>
        </div>
    </x-cn.crud>
</x-layout.app>
