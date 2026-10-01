<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components;

use Tests\TestCase;

final class CnNavigationBreadcrumbComponentTest extends TestCase
{
    public function test_breadcrumb_does_not_render_when_items_are_empty(): void
    {
        $view = $this->blade(
            '<x-cn.navigation.breadcrumb :items="$items" />',
            ['items' => []]
        );

        $view->assertDontSee('class="cn-breadcrumb"', false);
    }

    public function test_breadcrumb_renders_navigation_container(): void
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Dashboard', 'current' => true],
        ];

        $view = $this->blade(
            '<x-cn.navigation.breadcrumb :items="$items" />',
            ['items' => $items]
        );

        $view->assertSee('<nav', false);
        $view->assertSee('class="cn-breadcrumb"', false);
        $view->assertSee('aria-label="breadcrumb"', false);
        $view->assertSee(
            '<ol class="cn-breadcrumb__list">',
            false
        );
    }

    public function test_breadcrumb_renders_item_labels(): void
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Dashboard', 'current' => true],
        ];

        $view = $this->blade(
            '<x-cn.navigation.breadcrumb :items="$items" />',
            ['items' => $items]
        );

        $view->assertSee('Inicio');
        $view->assertSee('Dashboard');
    }

    public function test_breadcrumb_renders_link_when_item_has_url_and_is_not_current(): void
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Dashboard', 'current' => true],
        ];

        $view = $this->blade(
            '<x-cn.navigation.breadcrumb :items="$items" />',
            ['items' => $items]
        );

        $view->assertSee('href="/"', false);
        $view->assertSee('cn-breadcrumb__link', false);
        $view->assertSee('Inicio');
    }

    public function test_breadcrumb_current_item_is_not_a_link(): void
    {
        $items = [
            ['label' => 'Inicio', 'url' => '/'],
            [
                'label' => 'Dashboard',
                'url' => '/dashboard',
                'current' => true,
            ],
        ];

        $view = $this->blade(
            '<x-cn.navigation.breadcrumb :items="$items" />',
            ['items' => $items]
        );

        $view->assertSee('Dashboard');
        $view->assertSee('aria-current="page"', false);
        $view->assertDontSee('<a href="/dashboard"', false);
    }

    public function test_breadcrumb_current_item_has_current_class(): void
    {
        $items = [
            ['label' => 'Dashboard', 'current' => true],
        ];

        $view = $this->blade(
            '<x-cn.navigation.breadcrumb :items="$items" />',
            ['items' => $items]
        );

        $view->assertSee(
            'cn-breadcrumb__item--current',
            false
        );
    }
}
