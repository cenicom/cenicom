<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class ToolbarComponentTest extends TestCase
{
    public function test_renders_toolbar_container(): void
    {
        $view = $this->blade(
            '<x-cn.crud.toolbar />'
        );

        $view->assertSee(
            'cn-crud__toolbar',
            false
        );
    }

    public function test_renders_toolbar_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud.toolbar title="Acciones" />'
        );

        $view->assertSee(
            'Acciones'
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud.toolbar>
                <span>Contenido</span>
            </x-cn.crud.toolbar>'
        );

        $view->assertSee(
            'Contenido'
        );
    }

    public function test_does_not_render_title_when_not_provided(): void
    {
        $view = $this->blade(
            '<x-cn.crud.toolbar />'
        );

        $view->assertDontSee(
            'cn-crud__toolbar-title',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.crud.toolbar
                id="crud-toolbar"
            />'
        );

        $view->assertSee(
            'id="crud-toolbar"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.crud.toolbar
                class="custom-toolbar"
            />'
        );

        $view->assertSee(
            'cn-crud__toolbar',
            false
        );

        $view->assertSee(
            'custom-toolbar',
            false
        );
    }
}
