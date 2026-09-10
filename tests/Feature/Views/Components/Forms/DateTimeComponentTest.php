<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class DateTimeComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withViewErrors([]);
    }

    public function test_renders_datetime_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime name="created_at" />'
        );

        $view->assertSee(
            'type="datetime-local"',
            false
        );

        $view->assertSee(
            'name="created_at"',
            false
        );
    }

    public function test_uses_name_as_default_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime name="created_at" />'
        );

        $view->assertSee(
            'id="created_at"',
            false
        );
    }

    public function test_renders_custom_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime
                name="created_at"
                id="created-at"
            />'
        );

        $view->assertSee(
            'id="created-at"',
            false
        );
    }

    public function test_renders_value(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime
                name="created_at"
                value="2026-08-13T14:30"
            />'
        );

        $view->assertSee(
            'value="2026-08-13T14:30"',
            false
        );
    }

    public function test_renders_placeholder(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime
                name="created_at"
                placeholder="Seleccione fecha y hora"
            />'
        );

        $view->assertSee(
            'placeholder="Seleccione fecha y hora"',
            false
        );
    }

    public function test_renders_autocomplete(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime
                name="created_at"
                autocomplete="off"
            />'
        );

        $view->assertSee(
            'autocomplete="off"',
            false
        );
    }

    public function test_renders_required_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.datetime
                name="created_at"
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
            '<x-cn.forms.datetime
                name="created_at"
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
            '<x-cn.forms.datetime
                name="created_at"
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
            '<x-cn.forms.datetime
                name="created_at"
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
            '<x-cn.forms.datetime
                name="created_at"
                data-test="datetime-field"
            />'
        );

        $view->assertSee(
            'data-test="datetime-field"',
            false
        );
    }
}
