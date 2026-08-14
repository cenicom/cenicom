<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class CrudIntegrationTest extends TestCase
{
    public function test_renders_complete_crud_composition(): void
    {
        $view = $this->blade(
            <<<'BLADE'
<x-cn.crud
    title="Instituciones"
    subtitle="Administración de instituciones"
    icon="bi bi-building"
>
    <x-slot:toolbar>
        <x-cn.crud.toolbar title="Herramientas">
            <button type="button">Buscar</button>
        </x-cn.crud.toolbar>
    </x-slot:toolbar>

    <x-slot:filters>
        <x-cn.crud.filters title="Filtros">
            <input type="text" name="search">
        </x-cn.crud.filters>
    </x-slot:filters>

    <x-slot:actions>
        <x-cn.crud.actions create="Nueva institución">
            <button type="button">Editar</button>
        </x-cn.crud.actions>
    </x-slot:actions>

    <div id="crud-content">
        Contenido principal
    </div>

    <x-slot:footer>
        <div>Pie del CRUD</div>
    </x-slot:footer>
</x-cn.crud>
BLADE
        );

        $view->assertSee(
            'cn-crud',
            false
        );

        $view->assertSee(
            'Instituciones'
        );

        $view->assertSee(
            'Administración de instituciones'
        );

        $view->assertSee(
            'bi bi-building',
            false
        );

        $view->assertSee(
            'Herramientas'
        );

        $view->assertSee(
            'Buscar'
        );

        $view->assertSee(
            'Filtros'
        );

        $view->assertSee(
            'Nueva institución'
        );

        $view->assertSee(
            'Editar'
        );

        $view->assertSee(
            'Contenido principal'
        );

        $view->assertSee(
            'cn-crud__footer',
            false
        );

        $view->assertSee(
            'Pie del CRUD'
        );
    }

    public function test_renders_confirm_through_modal_composition(): void
    {
        $view = $this->blade(
            <<<'BLADE'
<x-cn.crud.confirm
    id="delete-confirm"
    title="Confirmar eliminación"
    message="¿Desea eliminar este registro?"
    confirm-text="Eliminar"
    cancel-text="Cancelar"
/>
BLADE
        );

        $view->assertSee(
            'cn-modal',
            false
        );

        $view->assertSee(
            'delete-confirm',
            false
        );

        $view->assertSee(
            'Confirmar eliminación'
        );

        $view->assertSee(
            '¿Desea eliminar este registro?'
        );

        $view->assertSee(
            'Eliminar'
        );

        $view->assertSee(
            'Cancelar'
        );
    }

    public function test_preserves_crud_structure_with_custom_attributes(): void
    {
        $view = $this->blade(
            <<<'BLADE'
<x-cn.crud
    title="Instituciones"
    class="custom-crud"
    id="institutions-crud"
>
    <div>Contenido</div>
</x-cn.crud>
BLADE
        );

        $view->assertSee(
            'cn-crud',
            false
        );

        $view->assertSee(
            'custom-crud',
            false
        );

        $view->assertSee(
            'id="institutions-crud"',
            false
        );

        $view->assertSee(
            'Contenido'
        );
    }
}
