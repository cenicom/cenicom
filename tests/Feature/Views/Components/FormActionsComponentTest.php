<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class FormActionsComponentTest extends TestCase
{
    public function test_renders_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn-form-actions>
                <button>Guardar</button>
            </x-cn-form-actions>'
        );

        $view->assertSee('Guardar');
        $view->assertSee('cn-form-actions', false);
    }

    public function test_preserves_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn-form-actions data-test="actions">
                Acciones
            </x-cn-form-actions>'
        );

        $view->assertSee('data-test="actions"', false);
        $view->assertSee('Acciones');
    }
}
