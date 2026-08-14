<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class ButtonComponentTest extends TestCase
{
    public function test_renders_button_element(): void
    {
        $view = $this->blade(
            '<x-cn.button>Guardar</x-cn.button>'
        );

        $view->assertSee('<button', false);
        $view->assertSee('Guardar');
    }

    public function test_renders_default_variant_and_size(): void
    {
        $view = $this->blade(
            '<x-cn.button>Guardar</x-cn.button>'
        );

        $view->assertSee('cn-button', false);
        $view->assertSee('cn-button--primary', false);
        $view->assertSee('cn-button--md', false);
    }

    public function test_supports_variant_and_size(): void
    {
        $view = $this->blade(
            '<x-cn.button variant="danger" size="lg">Eliminar</x-cn.button>'
        );

        $view->assertSee('cn-button--danger', false);
        $view->assertSee('cn-button--lg', false);
        $view->assertSee('Eliminar');
    }

    public function test_supports_button_type(): void
    {
        $view = $this->blade(
            '<x-cn.button type="submit">Guardar</x-cn.button>'
        );

        $view->assertSee(
            'type="submit"',
            false
        );
    }

    public function test_supports_disabled_state(): void
    {
        $view = $this->blade(
            '<x-cn.button disabled>Eliminar</x-cn.button>'
        );

        $view->assertSee(
            'cn-button--disabled',
            false
        );

        $view->assertSee(
            'disabled',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.button id="save-button" data-testid="save-button">Guardar</x-cn.button>'
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

    public function test_renders_link_when_href_is_provided(): void
    {
        $view = $this->blade(
            '<x-cn.button href="/institutions">Instituciones</x-cn.button>'
        );

        $view->assertSee(
            '<a',
            false
        );

        $view->assertSee(
            'href="/institutions"',
            false
        );

        $view->assertSee('Instituciones');
        $view->assertDontSee('<button', false);
    }

    public function test_renders_disabled_link_state(): void
    {
        $view = $this->blade(
            '<x-cn.button href="/institutions" disabled>Instituciones</x-cn.button>'
        );

        $view->assertSee(
            'href="#"',
            false
        );

        $view->assertSee(
            'aria-disabled="true"',
            false
        );

        $view->assertSee(
            'tabindex="-1"',
            false
        );
    }
}
