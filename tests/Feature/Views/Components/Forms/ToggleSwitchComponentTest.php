<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

final class ToggleSwitchComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        view()->share('errors', new ViewErrorBag());
    }

    public function test_renders_switch_component(): void
    {
        $view = $this->blade(
            '<x-cn.forms.toggleswitch name="active" />'
        );

        $view->assertSee('name="active"', false);
    }

    public function test_renders_checkbox_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.toggleswitch name="active" />'
        );

        $view->assertSee('type="checkbox"', false);
    }

    public function test_applies_switch_class(): void
    {
        $view = $this->blade(
            '<x-cn.forms.toggleswitch name="active" />'
        );

        $view->assertSee('cn-switch', false);
    }

    public function test_preserves_checked_state(): void
    {
        $view = $this->blade(
            '<x-cn.forms.toggleswitch
                name="active"
                :checked="true"
            />'
        );

        $view->assertSee('checked', false);
    }

    public function test_supports_custom_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.toggleswitch
                name="active"
                id="institution-active"
            />'
        );

        $view->assertSee('id="institution-active"', false);
    }
}
