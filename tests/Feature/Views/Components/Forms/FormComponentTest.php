<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class FormComponentTest extends TestCase
{
    public function test_renders_form_element(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            '<form',
            false
        );

        $view->assertSee(
            'action="/institutions"',
            false
        );
    }

    public function test_renders_form_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
            >
                <input name="name">
            </x-cn.forms.form>'
        );

        $view->assertSee(
            '<input name="name">',
            false
        );
    }

    public function test_uses_post_method_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'method="POST"',
            false
        );

        $view->assertSee(
            'name="_token"',
            false
        );
    }

    public function test_supports_get_method_without_csrf(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
                method="GET"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'method="GET"',
            false
        );

        $view->assertDontSee(
            'name="_token"',
            false
        );
    }

    public function test_supports_http_method_override(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions/1"
                method="PUT"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'method="POST"',
            false
        );

        $view->assertSee(
            'name="_method"',
            false
        );

        $view->assertSee(
            'value="PUT"',
            false
        );

        $view->assertSee(
            'name="_token"',
            false
        );
    }

    public function test_supports_form_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
                id="institution-form"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'id="institution-form"',
            false
        );
    }

    public function test_supports_autocomplete(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
                autocomplete="off"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'autocomplete="off"',
            false
        );
    }

    public function test_supports_novalidate(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
                novalidate
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'novalidate',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.form
                action="/institutions"
                data-testid="institution-form"
                aria-label="Formulario de institución"
            >
                Contenido
            </x-cn.forms.form>'
        );

        $view->assertSee(
            'data-testid="institution-form"',
            false
        );

        $view->assertSee(
            'aria-label="Formulario de institución"',
            false
        );
    }
}
