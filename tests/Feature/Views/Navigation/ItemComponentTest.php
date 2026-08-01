<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Navigation;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use Tests\TestCase;

final class ItemComponentTest extends TestCase
{
    public function test_renders_navigation_label(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.item :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('Instituciones');
    }

    public function test_renders_navigation_href(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'documentation',
            label: 'Documentación',
            type: NavigationNodeType::ITEM,
            url: 'https://example.com/docs',
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.item :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('href="https://example.com/docs"', false);
    }

    public function test_renders_icon_when_present(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
            icon: 'bi bi-building',
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.item :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('bi bi-building', false);
    }

    public function test_does_not_render_icon_when_absent(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.item :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertDontSee('<i', false);
    }
}
