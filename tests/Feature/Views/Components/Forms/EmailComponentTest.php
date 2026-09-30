<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

class EmailComponentTest extends TestCase
{
    public function test_email_component_renders_email_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.email name="email" />'
        );

        $view->assertSee('type="email"', false);
        $view->assertSee('name="email"', false);
        $view->assertSee('id="email"', false);
    }

    public function test_email_component_supports_required(): void
    {
        $view = $this->blade(
            '<x-cn.forms.email name="email" :required="true" />'
        );

        $view->assertSee('required', false);
    }

    public function test_email_component_supports_readonly(): void
    {
        $view = $this->blade(
            '<x-cn.forms.email name="email" :readonly="true" />'
        );

        $view->assertSee('readonly', false);
    }

    public function test_email_component_supports_minlength(): void
    {
        $view = $this->blade(
            '<x-cn.forms.email name="email" :minlength="5" />'
        );

        $view->assertSee('minlength="5"', false);
    }

    public function test_email_component_supports_maxlength(): void
    {
        $view = $this->blade(
            '<x-cn.forms.email name="email" :maxlength="100" />'
        );

        $view->assertSee('maxlength="100"', false);
    }
}
