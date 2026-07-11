<x-layout.app>

    <x-slot name="title">
        Detalle de Moneda
    </x-slot>

    <div class="cn-page">

        <header class="cn-page-header">

            <div class="cn-page-title">

                <div class="cn-page-icon">
                    <i class="fas fa-coins"></i>
                </div>

                <div>

                    <h1>Detalle de Moneda</h1>

                    <p>
                        Consulte la información registrada de la moneda.
                    </p>

                </div>

            </div>

        </header>

        <section class="cn-card">

            <div class="cn-card-body">

                <x-cn.group columns="2">

                    <x-cn.field>

                        <x-cn.label>
                            Código
                        </x-cn.label>

                        <x-cn.display :value="$currency->code" />

                    </x-cn.field>

                    <x-cn.field>

                        <x-cn.label>
                            Nombre
                        </x-cn.label>

                        <x-cn.display :value="$currency->name" />

                    </x-cn.field>

                    <x-cn.field>

                        <x-cn.label>
                            Símbolo
                        </x-cn.label>

                        <x-cn.display :value="$currency->symbol ?: '—'" />

                    </x-cn.field>

                    <x-cn.field>

                        <x-cn.label>
                            Estado
                        </x-cn.label>

                        <x-cn.display :value="$currency->status ? 'Activa' : 'Inactiva'" />

                    </x-cn.field>

                </x-cn.group>

                <x-cn.actions justify="between">

                    <x-cn.button :href="route('currencies.edit', $currency)">

                        <i class="fas fa-edit"></i>

                        Editar

                    </x-cn.button>

                    <x-cn.button :href="route('currencies.index')" variant="secondary">

                        <i class="fas fa-arrow-left"></i>

                        Regresar

                    </x-cn.button>

                </x-cn.actions>

            </div>

        </section>

    </div>

</x-layout.app>
