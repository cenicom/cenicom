<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

final class ToggleSwitchContractProbeTest extends TestCase
{
    public function test_toggleswitch_alias_resolves(): void
    {
        $html = Blade::render(
            '<x-cn.forms.toggleswitch name="active" />'
        );

        $this->assertStringContainsString(
            'name="active"',
            $html
        );
    }
}
