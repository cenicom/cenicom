<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class InputComponentTest extends TestCase
{
    public function test_renders_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.input
                id="currency-code"
                name="currency_code"
                type="text"
                value="COP"
            />'
        );

        $view->assertSee('<input', false);
        $view->assertSee('data-cn="input"', false);
    }

    public function test_renders_input_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.input
                id="currency-code"
                name="currency_code"
                type="text"
                value="COP"
                placeholder="Código"
                autocomplete="off"
                inputmode="text"
            />'
        );

        $view->assertSee('id="currency-code"', false);
        $view->assertSee('name="currency_code"', false);
        $view->assertSee('type="text"', false);
        $view->assertSee('value="COP"', false);
        $view->assertSee('placeholder="Código"', false);
        $view->assertSee('autocomplete="off"', false);
        $view->assertSee('inputmode="text"', false);
    }

    public function test_renders_valid_state_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.forms.input
                id="currency-code"
                name="currency_code"
                type="text"
                value="COP"
            />'
        );

        $view->assertSee('aria-invalid="false"', false);
        $view->assertDontSee('is-invalid', false);
    }

    public function test_renders_invalid_state_when_field_has_errors(): void
    {
        $this->withViewErrors([
            'currency_code' => 'El código de moneda es obligatorio.',
        ]);

        $view = $this->blade(
            '<x-cn.forms.input
                id="currency-code"
                name="currency_code"
                type="text"
                value="COP"
            />'
        );

        $view->assertSee('aria-invalid="true"', false);
        $view->assertSee('is-invalid', false);
    }

    public function test_supports_required_readonly_disabled_and_autofocus(): void
    {
        $view = $this->blade(
            '<x-cn.forms.input
                id="currency-code"
                name="currency_code"
                type="text"
                value="COP"
                required
                readonly
                disabled
                autofocus
            />'
        );

        $view->assertSee('required', false);
        $view->assertSee('readonly', false);
        $view->assertSee('disabled', false);
        $view->assertSee('autofocus', false);
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.input
                id="currency-code"
                name="currency_code"
                type="text"
                value="COP"
                class="custom-input"
                data-testid="currency-input"
            />'
        );

        $view->assertSee('custom-input', false);
        $view->assertSee('data-testid="currency-input"', false);
        $view->assertSee('cn-input', false);
    }
}
