<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class ButtonVariantsComponentTest extends TestCase
{
    public function test_renders_cancel_button(): void
    {
        $view = $this->blade(
            '<x-cn.button.cancel />'
        );

        $view->assertSee('cn-button--secondary', false);
        $view->assertSee('fa-times', false);
        $view->assertSee('Cancelar');
    }

    public function test_renders_create_button(): void
    {
        $view = $this->blade(
            '<x-cn.button.create />'
        );

        $view->assertSee('cn-button--primary', false);
        $view->assertSee('fa-plus', false);
        $view->assertSee('Nuevo');
    }

    public function test_renders_delete_button_as_submit(): void
    {
        $view = $this->blade(
            '<x-cn.button.delete />'
        );

        $view->assertSee('cn-button--danger', false);
        $view->assertSee('type="submit"', false);
        $view->assertSee('fa-trash', false);
        $view->assertSee('Eliminar');
    }

    public function test_renders_edit_button(): void
    {
        $view = $this->blade(
            '<x-cn.button.edit />'
        );

        $view->assertSee('cn-button--warning', false);
        $view->assertSee('fa-edit', false);
        $view->assertSee('Editar');
    }

    public function test_renders_save_button_as_submit(): void
    {
        $view = $this->blade(
            '<x-cn.button.save />'
        );

        $view->assertSee('cn-button--primary', false);
        $view->assertSee('type="submit"', false);
        $view->assertSee('fa-save', false);
        $view->assertSee('Guardar');
    }

    public function test_renders_show_button(): void
    {
        $view = $this->blade(
            '<x-cn.button.show />'
        );

        $view->assertSee('cn-button--info', false);
        $view->assertSee('fa-eye', false);
        $view->assertSee('Ver');
    }

    public function test_variants_support_custom_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.button.create>Crear institución</x-cn.button.create>'
        );

        $view->assertSee('Crear institución');
        $view->assertDontSee('Nuevo');
    }

    public function test_variants_forward_additional_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.button.save id="save-button" data-testid="save-button" />'
        );

        $view->assertSee(
            'id="save-button"',
            false
        );

        $view->assertSee(
            'data-testid="save-button"',
            false
        );
    }
}
