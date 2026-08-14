<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class TableHeadComponentTest extends TestCase
{
    public function test_renders_table_head(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.head>
                    <th>Nombre</th>
                </x-cn.table.head>
            </table>'
        );

        $view->assertSee(
            'Nombre'
        );
    }

    public function test_renders_table_head_element(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.head>
                    <th>Nombre</th>
                </x-cn.table.head>
            </table>'
        );

        $view->assertSee(
            '<thead',
            false
        );
    }

    public function test_supports_additional_html_attributes(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.head
                    id="table-head"
                    data-testid="table-head"
                >
                    <th>Nombre</th>
                </x-cn.table.head>
            </table>'
        );

        $view->assertSee(
            'id="table-head"',
            false
        );

        $view->assertSee(
            'data-testid="table-head"',
            false
        );
    }

    public function test_preserves_table_head_content(): void
    {
        $view = $this->blade(
            '<table>
                <x-cn.table.head>
                    <th>Nombre</th>
                    <th>Estado</th>
                </x-cn.table.head>
            </table>'
        );

        $view->assertSee('Nombre');
        $view->assertSee('Estado');
    }
}
