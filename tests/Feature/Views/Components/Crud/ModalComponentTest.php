<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use Tests\TestCase;

final class ModalComponentTest extends TestCase
{
    public function test_renders_modal_container(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal id="test-modal" />'
        );

        $view->assertSee(
            'cn-modal',
            false
        );

        $view->assertSee(
            'id="test-modal"',
            false
        );

        $view->assertSee(
            'modal fade',
            false
        );
    }

    public function test_renders_modal_title(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal
                id="test-modal"
                title="Confirmar acción"
            />'
        );

        $view->assertSee(
            'Confirmar acción'
        );

        $view->assertSee(
            'modal-title',
            false
        );
    }

    public function test_renders_slot_content_in_body(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal id="test-modal">
                <p>Contenido del modal</p>
            </x-cn.crud.modal>'
        );

        $view->assertSee(
            'Contenido del modal'
        );

        $view->assertSee(
            'modal-body',
            false
        );
    }

    public function test_renders_named_header(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal id="test-modal">
                <x-slot:header>
                    <strong>Encabezado</strong>
                </x-slot:header>
            </x-cn.crud.modal>'
        );

        $view->assertSee(
            'Encabezado'
        );

        $view->assertSee(
            'modal-header',
            false
        );
    }

    public function test_renders_named_body(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal id="test-modal">
                <x-slot:body>
                    <span>Cuerpo personalizado</span>
                </x-slot:body>
            </x-cn.crud.modal>'
        );

        $view->assertSee(
            'Cuerpo personalizado'
        );
    }

    public function test_named_body_takes_precedence_over_default_slot(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal id="test-modal">
                <x-slot:body>
                    Cuerpo nombrado
                </x-slot:body>

                Contenido del slot
            </x-cn.crud.modal>'
        );

        $view->assertSee(
            'Cuerpo nombrado'
        );

        $view->assertDontSee(
            'Contenido del slot'
        );
    }

    public function test_renders_named_footer(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal id="test-modal">
                <x-slot:footer>
                    <button type="button">Cerrar</button>
                </x-slot:footer>
            </x-cn.crud.modal>'
        );

        $view->assertSee(
            'Cerrar'
        );

        $view->assertSee(
            'modal-footer',
            false
        );
    }

    public function test_supports_small_size(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal
                id="test-modal"
                size="sm"
            />'
        );

        $view->assertSee(
            'modal-sm',
            false
        );
    }

    public function test_supports_large_size(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal
                id="test-modal"
                size="lg"
            />'
        );

        $view->assertSee(
            'modal-lg',
            false
        );
    }

    public function test_supports_extra_large_size(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal
                id="test-modal"
                size="xl"
            />'
        );

        $view->assertSee(
            'modal-xl',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal
                id="test-modal"
                data-test="modal"
            />'
        );

        $view->assertSee(
            'data-test="modal"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.crud.modal
                id="test-modal"
                class="custom-modal"
            />'
        );

        $view->assertSee(
            'cn-modal',
            false
        );

        $view->assertSee(
            'custom-modal',
            false
        );
    }
}
