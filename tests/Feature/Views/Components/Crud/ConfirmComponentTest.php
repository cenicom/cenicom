<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class ConfirmComponentTest extends TestCase
{
    public function test_renders_confirm_component(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm" form-id="delete-form" />'
        );

        $view->assertSee(
            'delete-confirm',
            false
        );
    }

    public function test_renders_default_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm" form-id="delete-form" />'
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
                form-id="delete-form"
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
            '<x-cn.crud.confirm id="delete-confirm" form-id="delete-form" />'
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
                form-id="delete-form"
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
                form-id="delete-form"
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
                form-id="delete-form"
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
            '<x-cn.crud.confirm id="delete-confirm" form-id="delete-form">
                <button type="submit">Eliminar registro</button>
            </x-cn.crud.confirm>'
        );

        $view->assertSee(
            'Eliminar registro'
        );
    }

    public function test_confirm_button_has_an_effective_submission_contract(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm id="delete-confirm" form-id="delete-form">
            <form id="delete-form" action="/institutions/1" method="POST">
                @csrf
                @method("DELETE")
            </form>
        </x-cn.crud.confirm>'
        );

        $view->assertSee('type="submit"', false);
    }

    public function test_confirm_button_is_associated_with_slot_form(): void
    {
        $view = $this->blade(
            '<x-cn.crud.confirm
                id="delete-confirm"
                form-id="delete-form"
            >

            <form id="delete-form" action="/institutions/1" method="POST">
                @csrf
                @method("DELETE")
            </form>
        </x-cn.crud.confirm>'
        );

        $view->assertSee(
            'type="submit"',
            false
        );

        $view->assertSee(
            'form="delete-form"',
            false
        );
    }


}
