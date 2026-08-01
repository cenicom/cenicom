<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Navigation;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use Tests\TestCase;

final class NodeComponentTest extends TestCase
{
    public function test_renders_group_node(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            icon: 'bi bi-gear',
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.node :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('cn-navigation-group', false);
        $view->assertSee('Administración');
    }

    public function test_renders_item_node(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
            icon: 'bi bi-building'
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.node :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('Instituciones');
        $view->assertSee('cn-navigation-item', false);
    }


}
