<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class HelpComponentTest extends TestCase
{
    public function test_renders_help_container(): void
    {
        $view = $this->blade(
            '<x-cn.forms.help>
                Información adicional
            </x-cn.forms.help>'
        );

        $view->assertSee(
            'cn-help',
            false
        );
    }

    public function test_renders_help_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.help>
                Ingrese el código de la institución.
            </x-cn.forms.help>'
        );

        $view->assertSee(
            'Ingrese el código de la institución.'
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.help
                id="institution-help"
                data-testid="help-text"
                aria-label="Ayuda"
            >
                Información adicional
            </x-cn.forms.help>'
        );

        $view->assertSee(
            'id="institution-help"',
            false
        );

        $view->assertSee(
            'data-testid="help-text"',
            false
        );

        $view->assertSee(
            'aria-label="Ayuda"',
            false
        );
    }

    public function test_renders_help_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.help>
                <strong>Importante:</strong>
                use únicamente códigos válidos.
            </x-cn.forms.help>'
        );

        $view->assertSee(
            '<strong>Importante:</strong>',
            false
        );

        $view->assertSee(
            'use únicamente códigos válidos.'
        );
    }
}
