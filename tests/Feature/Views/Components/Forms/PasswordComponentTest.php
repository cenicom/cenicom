<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

class PasswordComponentTest extends TestCase
{
    public function test_password_component_renders_password_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.password name="password" />'
        );

        $view->assertSee('type="password"', false);
        $view->assertSee('name="password"', false);
        $view->assertSee('id="password"', false);
    }

    public function test_password_component_supports_required(): void
    {
        $view = $this->blade(
            '<x-cn.forms.password name="password" :required="true" />'
        );

        $view->assertSee('required', false);
    }

    public function test_password_component_supports_readonly(): void
    {
        $view = $this->blade(
            '<x-cn.forms.password name="password" :readonly="true" />'
        );

        $view->assertSee('readonly', false);
    }

    public function test_password_component_supports_minlength(): void
    {
        $view = $this->blade(
            '<x-cn.forms.password name="password" :minlength="8" />'
        );

        $view->assertSee('minlength="8"', false);
    }

    public function test_password_component_supports_maxlength(): void
    {
        $view = $this->blade(
            '<x-cn.forms.password name="password" :maxlength="60" />'
        );

        $view->assertSee('maxlength="60"', false);
    }
}
