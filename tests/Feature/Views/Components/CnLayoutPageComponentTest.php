<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class CnLayoutPageComponentTest extends TestCase
{
    public function test_layout_page_component_renders(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page>Contenido de prueba</x-cn.layout.page>'
        );

        $view->assertSee('class="cn-page"', false);
    }

    public function test_layout_page_component_renders_cn_page_container(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page>Contenido de prueba</x-cn.layout.page>'
        );

        $view->assertSee('<div class="cn-page">', false);
    }

    public function test_layout_page_component_preserves_slot_content(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page>Contenido de prueba</x-cn.layout.page>'
        );

        $view->assertSee('Contenido de prueba');
    }
}
