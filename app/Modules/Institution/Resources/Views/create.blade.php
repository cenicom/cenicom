<x-layout.app>
    <x-cn.navigation.breadcrumb :items="[
        [
            'label' => 'Inicio',
            'url' => route('dashboard'),
        ],
        [
            'label' => 'Institución educativa',
            'url' => route('institutions.index'),
        ],
        [
            'label' => 'Crear institución',
            'current' => true,
        ],
    ]" />
    <x-cn.crud title="Crear institución" subtitle="Registro de una nueva institución educativa" icon="bi bi-building">
        <form method="POST" action="{{ route('institutions.store') }}" data-cn-confirm="create"
            data-cn-confirm-title="¿Guardar esta institución?"
            data-cn-confirm-text="¿Confirmas que deseas crear la institución con los datos ingresados?">
            @csrf

            <x-cn.forms.group columns="2">

                {{-- Código corto de la institución --}}
                <x-cn.forms.field id="institution-short-code-field">

                    <x-cn.forms.label for="institution-short-code" required>
                        Código corto
                    </x-cn.forms.label>

                    <x-cn.forms.input id="institution-short-code" name="shortCode" type="text" :value="old('shortCode')"
                        placeholder="Ej. IESBSC" required minlength="6" maxlength="15" />

                    <x-cn.forms.help id="institution-short-code-help">
                        Abreviatura institucional en letras mayúsculas, entre 6 y 15 caracteres.
                    </x-cn.forms.help>

                    <x-cn.forms.error for="shortCode" />

                </x-cn.forms.field>

            </x-cn.forms.group>

            @include('institutions::_form')

            <div class="mt-4 d-flex gap-2">

                <x-cn.button.save type="submit">
                    Crear institución
                </x-cn.button.save>

                <x-cn.button.cancel href="{{ route('institutions.index') }}">
                    Cancelar
                </x-cn.button.cancel>

            </div>
        </form>
    </x-cn.crud>
</x-layout.app>
