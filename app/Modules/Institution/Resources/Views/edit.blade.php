<x-cn.crud
    title="Editar institución"
    subtitle="Actualización de la información de la institución educativa"
    icon="bi bi-building"
>
    <form
        method="POST"
        action="{{ route('institutions.update', $institution) }}"
    >
        @csrf
        @method('PUT')

        @include('institutions::_form')

        <div class="mt-4 d-flex gap-2">

            <x-cn.button.save
                type="submit"
            >
                Guardar cambios
            </x-cn.button.save>

            <x-cn.button.cancel
                href="{{ route('institutions.index') }}"
            >
                Cancelar
            </x-cn.button.cancel>

        </div>
    </form>
</x-cn.crud>
