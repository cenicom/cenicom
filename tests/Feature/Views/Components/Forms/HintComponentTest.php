<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class HintComponentTest extends TestCase
{
    public function test_renders_hint_container(): void
    {
        $view = $this->blade(
            '<x-cn.forms.hint>
                Texto de ayuda.
            </x-cn.forms.hint>'
        );

        $view->assertSee(
            'cn-hint',
            false
        );
    }

    public function test_renders_hint_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.hint>
                Usa entre 8 y 20 caracteres.
            </x-cn.forms.hint>'
        );

        $view->assertSee(
            'Usa entre 8 y 20 caracteres.'
        );
    }

    public function test_supports_hint_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.hint id="password-hint">
                Usa entre 8 y 20 caracteres.
            </x-cn.forms.hint>'
        );

        $view->assertSee(
            'id="password-hint"',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.hint
                id="password-hint"
                data-testid="hint-message"
                aria-live="polite"
            >
                Usa entre 8 y 20 caracteres.
            </x-cn.forms.hint>'
        );

        $view->assertSee(
            'data-testid="hint-message"',
            false
        );

        $view->assertSee(
            'aria-live="polite"',
            false
        );
    }

    public function test_renders_hint_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.hint>
                <strong>Importante:</strong>
                usa una contraseña segura.
            </x-cn.forms.hint>'
        );

        $view->assertSee(
            '<strong>Importante:</strong>',
            false
        );

        $view->assertSee(
            'usa una contraseña segura.'
        );
    }
}
