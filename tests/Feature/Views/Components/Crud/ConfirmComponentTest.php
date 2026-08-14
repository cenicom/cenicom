<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class ConfirmComponentTest extends TestCase
{
    public function test_renders_confirm_component(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm" />'
        );

        $view->assertSee(
            'delete-confirm',
            false
        );
    }

    public function test_renders_default_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm" />'
        );

        $view->assertSee(
            'Confirmar acción'
        );
    }

    public function test_renders_custom_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm
                id="delete-confirm"
                title="Eliminar registro"
            />'
        );

        $view->assertSee(
            'Eliminar registro'
        );
    }

    public function test_renders_default_message(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm" />'
        );

        $view->assertSee(
            '¿Está seguro de continuar?'
        );
    }

    public function test_renders_custom_message(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm
                id="delete-confirm"
                message="¿Desea eliminar esta institución?"
            />'
        );

        $view->assertSee(
            '¿Desea eliminar esta institución?'
        );
    }

    public function test_renders_custom_confirm_text(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm
                id="delete-confirm"
                confirm-text="Eliminar"
            />'
        );

        $view->assertSee(
            'Eliminar'
        );
    }

    public function test_renders_custom_cancel_text(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm
                id="delete-confirm"
                cancel-text="No eliminar"
            />'
        );

        $view->assertSee(
            'No eliminar'
        );
    }

    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm">
                <button type="submit">Eliminar registro</button>
            </x-cn.crud.confirm>'
        );

        $view->assertSee(
            'Eliminar registro'
        );
    }
}
