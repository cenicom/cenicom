<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Forms;

use Tests\TestCase;

final class DisplayComponentTest extends TestCase
{
    public function test_renders_display_container(): void
    {
        $view = $this->blade(
            '<x-cn.forms.display>
                Valor mostrado
            </x-cn.forms.display>'
        );

        $view->assertSee(
            'cn-display',
            false
        );
    }

    public function test_renders_display_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.display>
                CENICOM ERP
            </x-cn.forms.display>'
        );

        $view->assertSee(
            'CENICOM ERP'
        );
    }

    public function test_supports_display_id(): void
    {
        $view = $this->blade(
            '<x-cn.forms.display id="institution-name">
                CENICOM ERP
            </x-cn.forms.display>'
        );

        $view->assertSee(
            'id="institution-name"',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.forms.display
                id="institution-name"
                data-testid="display-value"
                aria-label="Nombre de institución"
            >
                CENICOM ERP
            </x-cn.forms.display>'
        );

        $view->assertSee(
            'data-testid="display-value"',
            false
        );

        $view->assertSee(
            'aria-label="Nombre de institución"',
            false
        );
    }

    public function test_renders_display_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.forms.display>
                <strong>CENICOM</strong>
                <span>ERP</span>
            </x-cn.forms.display>'
        );

        $view->assertSee(
            '<strong>CENICOM</strong>',
            false
        );

        $view->assertSee(
            '<span>ERP</span>',
            false
        );
    }
}
