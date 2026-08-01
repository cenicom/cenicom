<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Navigation;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use Tests\TestCase;

final class SidebarComponentTest extends TestCase
{
    public function test_renders_sidebar_container(): void
    {
        // Arrange

        $navigation = new NavigationTreeData();

        // Act

        $view = $this->blade(
            '<x-cn.navigation.sidebar :navigation="$navigation" />',
            [
                'navigation' => $navigation,
            ]
        );

        // Assert

        $view->assertSee('cn-sidebar', false);
        $view->assertSee('<nav>', false);
    }

    public function test_renders_navigation_nodes(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
        );

        $navigation = new NavigationTreeData(
            nodes: [
                $node,
            ]
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.sidebar :navigation="$navigation" />',
            [
                'navigation' => $navigation,
            ]
        );

        // Assert

        $view->assertSee('Instituciones');
    }

    public function test_renders_empty_navigation(): void
    {
        // Arrange

        $navigation = new NavigationTreeData();

        // Act

        $view = $this->blade(
            '<x-cn.navigation.sidebar :navigation="$navigation" />',
            [
                'navigation' => $navigation,
            ]
        );

        // Assert

        $view->assertSee('cn-sidebar', false);
        $view->assertDontSee('cn-navigation-item', false);
        $view->assertDontSee('cn-navigation-group', false);
    }
}
