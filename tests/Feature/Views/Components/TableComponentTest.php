<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class TableComponentTest extends TestCase
{
    public function test_renders_table_container(): void
    {
        $view = $this->blade(
            '<x-cn.table>
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'cn-table',
            false
        );
    }

    public function test_renders_table_content(): void
    {
        $view = $this->blade(
            '<x-cn.table>
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'Contenido'
        );
    }

    public function test_uses_responsive_wrapper_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.table>
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'cn-table-wrapper',
            false
        );
    }

    public function test_supports_non_responsive_table(): void
    {
        $view = $this->blade(
            '<x-cn.table :responsive="false">
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertDontSee(
            'cn-table-wrapper',
            false
        );
    }

    public function test_supports_striped_variant(): void
    {
        $view = $this->blade(
            '<x-cn.table :striped="true">
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'cn-table--striped',
            false
        );
    }

    public function test_supports_hover_variant_by_default(): void
    {
        $view = $this->blade(
            '<x-cn.table>
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'cn-table--hover',
            false
        );
    }

    public function test_can_disable_hover_variant(): void
    {
        $view = $this->blade(
            '<x-cn.table :hover="false">
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertDontSee(
            'cn-table--hover',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<x-cn.table
                id="institutions-table"
                data-testid="table"
            >
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'id="institutions-table"',
            false
        );

        $view->assertSee(
            'data-testid="table"',
            false
        );
    }

    public function test_preserves_base_class_with_custom_class(): void
    {
        $view = $this->blade(
            '<x-cn.table class="custom-table">
                <tr><td>Contenido</td></tr>
            </x-cn.table>'
        );

        $view->assertSee(
            'cn-table',
            false
        );

        $view->assertSee(
            'custom-table',
            false
        );
    }
}
