<x-cn.layout.page
    title="Usuarios"
    icon="users"
    description="Demostración del Data Kit CENICOM">

    <x-cn.patterns.crud
        title="Administración de Usuarios"
        subtitle="Pantalla de demostración">

        <x-cn.data.grid>

            {{-- Toolbar --}}
            <x-slot:toolbar>

                <x-cn.data.toolbar>

                    <x-slot:left>

                        <x-cn.forms.input
                            name="search"
                            icon="magnifying-glass"
                            placeholder="Buscar usuario..." />

                    </x-slot:left>

                    <x-slot:right>

                        <x-cn.button
                            variant="success"
                            icon="file-excel">

                            Excel

                        </x-cn.button>

                        <x-cn.button
                            variant="danger"
                            icon="file-pdf">

                            PDF

                        </x-cn.button>

                        <x-cn.button
                            icon="plus">

                            Nuevo

                        </x-cn.button>

                    </x-slot:right>

                </x-cn.data.toolbar>

            </x-slot:toolbar>

            {{-- Tabla --}}

            <x-cn.data.table>

                <table class="cn-table-grid">

                    <thead>

                        <tr>

                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>Luis González</td>

                            <td>luis@cenicom.com</td>

                            <td>Administrador</td>

                            <td>

                                <x-cn.data.badge
                                    variant="success">

                                    Activo

                                </x-cn.data.badge>

                            </td>

                        </tr>

                        <tr>

                            <td>María Pérez</td>

                            <td>maria@cenicom.com</td>

                            <td>Docente</td>

                            <td>

                                <x-cn.data.badge
                                    variant="warning">

                                    Vacaciones

                                </x-cn.data.badge>

                            </td>

                        </tr>

                        <tr>

                            <td>Carlos Ruiz</td>

                            <td>carlos@cenicom.com</td>

                            <td>Secretaría</td>

                            <td>

                                <x-cn.data.badge
                                    variant="danger">

                                    Inactivo

                                </x-cn.data.badge>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </x-cn.data.table>

            {{-- Paginación --}}

            <x-slot:pagination>

                <x-cn.data.pagination
                    :total="3"
                    :from="1"
                    :to="3"/>

            </x-slot:pagination>

        </x-cn.data.grid>

    </x-cn.patterns.crud>

</x-cn.layout.page>
