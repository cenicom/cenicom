<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class CrudComponentTest extends TestCase
{
    public function test_renders_crud_container(): void
    {
        $view = $this->blade(
            '<x-cn.crud />'
        );

        $view->assertSee(
            'cn-crud',
            false
        );

        $view->assertSee(
            'container',
            false
        );
    }

    public function test_renders_crud_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud title="Instituciones" />'
        );

        $view->assertSee(
            'Instituciones'
        );
    }

    public function test_renders_crud_subtitle(): void
    {
        $view = $this->blade(
            '<x-cn.crud subtitle="Administración de instituciones" />'
        );

        $view->assertSee(
            'Administración de instituciones'
        );
    }

    public function test_renders_crud_icon(): void
    {
        $view = $this->blade(
            '<x-cn.crud icon="bi bi-building" />'
        );

        $view->assertSee(
            'bi bi-building',
            false
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud>
                Contenido principal
            </x-cn.crud>'
        );

        $view->assertSee(
            'Contenido principal'
        );
    }

    public function test_renders_fluid_container(): void
    {
        $view = $this->blade(
            '<x-cn.crud fluid="true" />'
        );

        $view->assertSee(
            'container-fluid',
            false
        );
    }

    public function test_renders_toolbar_slot(): void
    {
        $view = $this->blade(
            <<<'BLADE'
        <x-cn.crud>
            <x-slot:toolbar>
                Toolbar content
            </x-slot:toolbar>
        </x-cn.crud>
        BLADE
        );

        $view->assertSee('Toolbar content');
    }

    public function test_renders_filters_slot(): void
    {
        $view = $this->blade(
            <<<'BLADE'
        <x-cn.crud>
            <x-slot:filters>
                Filters content
            </x-slot:filters>
        </x-cn.crud>
        BLADE
        );

        $view->assertSee('Filters content');
    }

    public function test_renders_actions_slot(): void
    {
        $view = $this->blade(
            <<<'BLADE'
        <x-cn.crud>
            <x-slot:actions>
                Actions content
            </x-slot:actions>
        </x-cn.crud>
        BLADE
        );

        $view->assertSee('Actions content');
    }

    public function test_renders_footer_slot(): void
    {
        $view = $this->blade(
            <<<'BLADE'
        <x-cn.crud>
            <x-slot:footer>
                Footer content
            </x-slot:footer>
        </x-cn.crud>
        BLADE
        );

        $view->assertSee('Footer content');
    }

    public function test_renders_empty_slot_instead_of_main_content(): void
    {
        $view = $this->blade(
            <<<'BLADE'
        <x-cn.crud>
            <x-slot:empty>
                Empty content
            </x-slot:empty>

            Main content
        </x-cn.crud>
        BLADE
        );

        $view->assertSee('Empty content');
        $view->assertDontSee('Main content');
    }
}
