<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;


use Tests\TestCase;

final class ErrorComponentTest extends TestCase
{
    public function test_renders_error_container(): void
    {
        $view = $this->withViewErrors([
            'email' => 'El correo es obligatorio.',
        ])->blade(
            '<x-cn.forms.error for="email" />'
        );

        $view->assertSee('cn-error', false);
        $view->assertSee('role="alert"', false);
    }

    public function test_renders_error_content(): void
    {
        $view = $this->withViewErrors([
            'email' => 'El correo es obligatorio.',
        ])->blade(
            '<x-cn.forms.error for="email" />'
        );

        $view->assertSee('El correo es obligatorio.');
    }

    public function test_supports_error_id(): void
    {
        $view = $this->withViewErrors([
            'email' => 'El correo es obligatorio.',
        ])->blade(
            '<x-cn.forms.error for="email" />'
        );

        $view->assertSee('id="email-error"', false);
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->withViewErrors([
            'email' => 'El correo es obligatorio.',
        ])->blade(
            '<x-cn.forms.error
                for="email"
                data-test="email-error"
                class="custom-class"
            />'
        );

        $view->assertSee('data-test="email-error"', false);
        $view->assertSee('custom-class', false);
    }

    public function test_renders_error_slot_content(): void
    {
        $view = $this->withViewErrors([
            'email' => 'El correo es obligatorio.',
        ])->blade(
            '<x-cn.forms.error for="email">
                Contenido adicional
            </x-cn.forms.error>'
        );

        $view->assertSee('El correo es obligatorio.');
    }
}
