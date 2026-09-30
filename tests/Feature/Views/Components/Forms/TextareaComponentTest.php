<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

final class TextareaComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_textarea_element(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea name="description" />'
        );

        $view->assertSee('<textarea', false);
        $view->assertSee('name="description"', false);
        $view->assertSee('id="description"', false);
    }

    public function test_uses_name_as_default_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea name="description" />'
        );

        $view->assertSee('id="description"', false);
    }

    public function test_renders_custom_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
                id="custom-description"
            />'
        );

        $view->assertSee('id="custom-description"', false);
        $view->assertSee('name="description"', false);
    }

    public function test_renders_value(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
                value="Texto inicial"
            />'
        );

        $view->assertSee('>Texto inicial</textarea>', false);
    }

    public function test_preserves_old_value(): void
    {
        $request = Request::create('/');

        $request->setLaravelSession($this->app['session.store']);

        $this->app->instance('request', $request);

        $this->app['session']->put('_old_input', [
            'description' => 'Valor anterior',
        ]);

        $view = $this->blade(
            '<x-cn.forms.textarea
            name="description"
            value="Valor inicial"
        />'
        );

        $view->assertSee('>Valor anterior</textarea>', false);
        $view->assertDontSee('>Valor inicial</textarea>', false);
    }

    public function test_renders_placeholder_and_rows(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
                placeholder="Ingrese una descripción"
                :rows="6"
            />'
        );

        $view->assertSee('placeholder="Ingrese una descripción"', false);
        $view->assertSee('rows="6"', false);
    }

    public function test_renders_length_constraints(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
                :maxlength="500"
                :minlength="10"
            />'
        );

        $view->assertSee('maxlength="500"', false);
        $view->assertSee('minlength="10"', false);
    }

    public function test_renders_required_readonly_disabled_and_autofocus_states(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
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

    public function test_renders_valid_aria_state_without_errors(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea name="description" />'
        );

        $view->assertSee('aria-invalid="false"', false);
        $view->assertSee('class="cn-textarea"', false);
    }

    public function test_renders_invalid_aria_state_when_validation_fails(): void
    {
        $view = $this->withViewErrors([
            'description' => 'La descripción es obligatoria.',
        ])->blade(
            '<x-cn.forms.textarea name="description" />'
        );

        $view->assertSee('aria-invalid="true"', false);
        $view->assertSee('is-invalid', false);
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
                data-test="textarea"
                class="custom-class"
            />'
        );

        $view->assertSee('data-test="textarea"', false);
        $view->assertSee('custom-class', false);
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.forms.textarea
                name="description"
                class="custom-class"
            />'
        );

        $view->assertSee('cn-textarea', false);
        $view->assertSee('custom-class', false);
    }
}
