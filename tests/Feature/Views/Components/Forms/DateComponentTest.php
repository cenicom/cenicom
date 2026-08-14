<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class DateComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withViewErrors([]);
    }

    public function test_renders_date_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date name="birth_date" />'
        );

        $view->assertSee(
            'type="date"',
            false
        );

        $view->assertSee(
            'name="birth_date"',
            false
        );
    }

    public function test_uses_name_as_default_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date name="birth_date" />'
        );

        $view->assertSee(
            'id="birth_date"',
            false
        );
    }

    public function test_renders_custom_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                id="date-of-birth"
            />'
        );

        $view->assertSee(
            'id="date-of-birth"',
            false
        );
    }

    public function test_renders_value(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                value="2026-08-13"
            />'
        );

        $view->assertSee(
            'value="2026-08-13"',
            false
        );
    }

    public function test_renders_minimum_date(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                min="2020-01-01"
            />'
        );

        $view->assertSee(
            'min="2020-01-01"',
            false
        );
    }

    public function test_renders_maximum_date(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                max="2030-12-31"
            />'
        );

        $view->assertSee(
            'max="2030-12-31"',
            false
        );
    }

    public function test_forces_numeric_input_mode(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date name="birth_date" />'
        );

        $view->assertSee(
            'inputmode="numeric"',
            false
        );
    }

    public function test_renders_placeholder(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                placeholder="Seleccione una fecha"
            />'
        );

        $view->assertSee(
            'placeholder="Seleccione una fecha"',
            false
        );
    }

    public function test_renders_autocomplete(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                autocomplete="bday"
            />'
        );

        $view->assertSee(
            'autocomplete="bday"',
            false
        );
    }

    public function test_renders_required_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                :required="true"
            />'
        );

        $view->assertSee(
            'required',
            false
        );
    }

    public function test_renders_readonly_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                :readonly="true"
            />'
        );

        $view->assertSee(
            'readonly',
            false
        );
    }

    public function test_renders_disabled_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                :disabled="true"
            />'
        );

        $view->assertSee(
            'disabled',
            false
        );
    }

    public function test_renders_autofocus_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                :autofocus="true"
            />'
        );

        $view->assertSee(
            'autofocus',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.date
                name="birth_date"
                data-test="date-field"
            />'
        );

        $view->assertSee(
            'data-test="date-field"',
            false
        );
    }
}
