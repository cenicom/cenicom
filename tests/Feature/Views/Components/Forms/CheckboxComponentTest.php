<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class CheckboxComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withViewErrors([]);
    }

    public function test_renders_checkbox_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox name="active" />'
        );

        $view->assertSee(
            'type="checkbox"',
            false
        );

        $view->assertSee(
            'name="active"',
            false
        );
    }

    public function test_uses_name_as_default_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox name="active" />'
        );

        $view->assertSee(
            'id="active"',
            false
        );
    }

    public function test_renders_custom_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                id="is-active"
            />'
        );

        $view->assertSee(
            'id="is-active"',
            false
        );
    }

    public function test_renders_custom_value(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                value="yes"
            />'
        );

        $view->assertSee(
            'value="yes"',
            false
        );
    }

    public function test_renders_checked_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                :checked="true"
            />'
        );

        $view->assertSee(
            'checked',
            false
        );
    }

    public function test_renders_disabled_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                :disabled="true"
            />'
        );

        $view->assertSee(
            'disabled',
            false
        );
    }

    public function test_renders_required_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                :required="true"
            />'
        );

        $view->assertSee(
            'required',
            false
        );
    }

    public function test_renders_valid_aria_state_without_errors(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox name="active" />'
        );

        $view->assertSee(
            'aria-invalid="false"',
            false
        );
    }

    public function test_renders_invalid_aria_state_when_validation_fails(): void
    {
        $view = $this->withViewErrors([
            'active' => 'El campo es obligatorio.',
        ])->blade(
            '<x-cn.forms.checkbox name="active" />'
        );

        $view->assertSee(
            'aria-invalid="true"',
            false
        );

        $view->assertSee(
            'is-invalid',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                data-test="checkbox"
            />'
        );

        $view->assertSee(
            'data-test="checkbox"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.forms.checkbox
                name="active"
                class="custom-checkbox"
            />'
        );

        $view->assertSee(
            'cn-checkbox',
            false
        );

        $view->assertSee(
            'custom-checkbox',
            false
        );
    }
}
