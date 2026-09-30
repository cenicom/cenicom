<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class NumberComponentTest extends TestCase
{
    public function test_renders_number_input(): void
    {
        $view = $this->blade(
            '<x-cn.forms.number
                name="amount"
                id="amount"
            />'
        );

        $view->assertSee(
            'type="number"',
            false
        );

        $view->assertSee(
            'name="amount"',
            false
        );

        $view->assertSee(
            'id="amount"',
            false
        );
    }
}
