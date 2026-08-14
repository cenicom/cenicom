<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class ButtonBackComponentTest extends TestCase
{
    public function test_renders_back_button(): void
    {
        $view = $this->blade(
            '<x-cn.button.back />'
        );

        $view->assertSee('cn-button', false);
        $view->assertSee('cn-button--secondary', false);
        $view->assertSee('Atrás');
    }

    public function test_renders_back_icon(): void
    {
        $view = $this->blade(
            '<x-cn.button.back />'
        );

        $view->assertSee('fa-arrow-left', false);
    }

    public function test_supports_custom_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.button.back>Volver al paso anterior</x-cn.button.back>'
        );

        $view->assertSee('Volver al paso anterior');
        $view->assertDontSee('Atrás');
    }

    public function test_supports_button_type(): void
    {
        $view = $this->blade(
            '<x-cn.button.back type="button" />'
        );

        $view->assertSee('type="button"', false);
    }

    public function test_supports_href_for_navigation(): void
    {
        $view = $this->blade(
            '<x-cn.button.back href="/institutions/step/1" />'
        );

        $view->assertSee(
            'href="/institutions/step/1"',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.button.back id="back-button" data-testid="wizard-back" />'
        );

        $view->assertSee(
            'id="back-button"',
            false
        );

        $view->assertSee(
            'data-testid="wizard-back"',
            false
        );
    }

    public function test_supports_disabled_state(): void
    {
        $view = $this->blade(
            '<x-cn.button.back disabled />'
        );

        $view->assertSee(
            'cn-button--disabled',
            false
        );

        $view->assertSee(
            'aria-disabled="true"',
            false
        );
    }
}
