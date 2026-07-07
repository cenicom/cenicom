<x-cn.layout.page title="Dashboard" icon="house" description="Bienvenido nuevamente al sistema.">

    <x-slot:actions>

        <x-cn.button>

            Nuevo

        </x-cn.button>

    </x-slot:actions>

    <x-cn.card>

        Bienvenido al ERP.

    </x-cn.card>
    <x-cn.card title="Prueba CN Input">

        <x-cn.forms.input name="nombre" label="Nombre" icon="user" placeholder="Ingrese su nombre"
            hint="Este será el nombre del usuario." required />

    </x-cn.card>

</x-cn.layout.page>
