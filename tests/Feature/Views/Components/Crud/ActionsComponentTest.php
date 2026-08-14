<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class ActionsComponentTest extends TestCase
{
    public function test_renders_actions_container_with_create_action(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions create="Nuevo registro" />'
        );

        $view->assertSee(
            'cn-crud__actions',
            false
        );
    }

    public function test_renders_create_action_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions create="Nuevo registro" />'
        );

        $view->assertSee(
            'Nuevo registro'
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions>
                <span>Editar</span>
            </x-cn.crud.actions>'
        );

        $view->assertSee(
            'Editar'
        );
    }

    public function test_does_not_render_when_empty(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions />'
        );

        $view->assertDontSee(
            'cn-crud__actions',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions
                create="Nuevo"
                id="crud-actions"
            />'
        );

        $view->assertSee(
            'id="crud-actions"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions
                create="Nuevo"
                class="custom-actions"
            />'
        );

        $view->assertSee(
            'cn-crud__actions',
            false
        );

        $view->assertSee(
            'custom-actions',
            false
        );
    }

    public function test_create_action_renders_cn_button(): void
    {
        $view = $this->blade(
            '<x-cn.crud.actions create="Nuevo registro" />'
        );

        $view->assertSee(
            'Nuevo registro'
        );

        $view->assertSee(
            'fa-plus',
            false
        );
    }
}
