<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class CnPageHeaderComponentTest extends TestCase
{
    public function test_page_header_renders(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page-header title="Dashboard" />'
        );

        $view->assertSee('cn-page-header', false);
    }

    public function test_page_header_renders_title(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page-header title="Dashboard" />'
        );

        $view->assertSee('Dashboard');
    }

    public function test_page_header_renders_subtitle_when_provided(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page-header
                title="Dashboard"
                subtitle="Resumen general de la institución"
            />'
        );

        $view->assertSee('Dashboard');
        $view->assertSee('Resumen general de la institución');
    }

    public function test_page_header_does_not_render_empty_subtitle(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page-header
                title="Dashboard"
                subtitle=""
            />'
        );

        $view->assertDontSee('cn-page-header__subtitle', false);
    }

    public function test_page_header_renders_icon_when_provided(): void
    {
        $view = $this->blade(
            '<x-cn.layout.page-header
                title="Dashboard"
                icon="dashboard"
            />'
        );

        $view->assertSee('dashboard');
    }
}
