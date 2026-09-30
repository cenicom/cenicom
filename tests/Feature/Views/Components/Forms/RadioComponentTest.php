<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class RadioComponentTest extends TestCase
{
    public function test_renders_radio_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.radio
                name="status"
                value="active"
            />'
        );

        $view->assertSee(
            'type="radio"',
            false
        );

        $view->assertSee(
            'name="status"',
            false
        );

        $view->assertSee(
            'value="active"',
            false
        );
    }

    public function test_generates_id_when_not_provided(): void
    {
        $view = $this->blade(
            '<x-cn.forms.radio
                name="status"
                value="Activo"
            />'
        );

        $view->assertSee(
            'id="status_Activo"',
            false
        );
    }

    public function test_supports_checked_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.radio
                name="status"
                value="active"
                :checked="true"
            />'
        );

        $view->assertSee(
            'checked',
            false
        );
    }

    public function test_supports_required_and_disabled(): void
    {
        $view = $this->blade(
            '<x-cn.forms.radio
                name="status"
                value="active"
                :required="true"
                :disabled="true"
            />'
        );

        $view->assertSee(
            'required',
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
            '<x-cn.forms.radio
                name="status"
                value="active"
                data-testid="status-active"
                aria-label="Activo"
            />'
        );

        $view->assertSee(
            'data-testid="status-active"',
            false
        );

        $view->assertSee(
            'aria-label="Activo"',
            false
        );
    }
}
