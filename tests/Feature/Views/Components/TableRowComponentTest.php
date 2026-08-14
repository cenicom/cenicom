<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class TableRowComponentTest extends TestCase
{
    public function test_renders_table_row(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.row>
                    <td>Institución</td>
                </x-cn.table.row>
            </table>'
        );

        $view->assertSee(
            '<tr',
            false
        );
    }

    public function test_renders_row_content(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.row>
                    <td>Institución</td>
                </x-cn.table.row>
            </table>'
        );

        $view->assertSee('Institución');
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.row
                    id="institution-row"
                    data-testid="table-row"
                >
                    <td>Institución</td>
                </x-cn.table.row>
            </table>'
        );

        $view->assertSee(
            'id="institution-row"',
            false
        );

        $view->assertSee(
            'data-testid="table-row"',
            false
        );
    }

    public function test_preserves_row_content(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.row>
                    <td>Institución</td>
                    <td>Activa</td>
                </x-cn.table.row>
            </table>'
        );

        $view->assertSee('Institución');
        $view->assertSee('Activa');
    }
}
