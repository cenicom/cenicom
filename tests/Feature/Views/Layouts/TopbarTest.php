<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Layouts;

use Tests\TestCase;

final class TopbarTest extends TestCase
{
    public function test_renders_topbar(): void
    {
        $view = $this->blade(
            '<x-layouts.topbar />'
        );

        $view->assertSee(
            'cn-topbar',
            false
        );
    }

    public function test_renders_application_name(): void
    {
        config([
            'app.name' => 'CENICOM ERP',
        ]);

        $view = $this->blade(
            '<x-layouts.topbar />'
        );

        $view->assertSee('CENICOM ERP');
    }

    public function test_renders_topbar_content_container(): void
    {
        $view = $this->blade(
            '<x-layouts.topbar />'
        );

        $view->assertSee(
            'cn-topbar-content',
            false
        );
    }

    public function test_renders_topbar_brand_and_actions_containers(): void
    {
        $view = $this->blade(
            '<x-layouts.topbar />'
        );

        $view->assertSee(
            'cn-topbar-brand',
            false
        );

        $view->assertSee(
            'cn-topbar-actions',
            false
        );
    }

    public function test_renders_topbar_slot_content(): void
    {
        $view = $this->blade(
            '<x-layouts.topbar>
            <button>Acción</button>
        </x-layouts.topbar>'
        );

        $view->assertSee('Acción');
    }
}
