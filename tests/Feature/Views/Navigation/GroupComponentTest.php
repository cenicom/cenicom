<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Navigation;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use Tests\TestCase;

final class GroupComponentTest extends TestCase
{
    public function test_renders_group_label(): void
    {
        // Arrange

        $node = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.group :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('Administración');
    }

    public function test_renders_group_icon_when_present(): void
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
            '<x-cn.navigation.group :node="$node" />',
            [
                'node' => $node,
            ]
        );

        // Assert

        $view->assertSee('bi bi-gear', false);
    }

    public function test_renders_children_when_available(): void
    {
        // Arrange

        $child = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
        );

        $group = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            children: [
                $child,
            ],
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.group :node="$node" />',
            [
                'node' => $group,
            ]
        );

        // Assert

        $view->assertSee('cn-navigation-children', false);
        $view->assertSee('Instituciones');
    }

    public function test_does_not_render_children_list_when_empty(): void
    {
        // Arrange

        $group = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.group :node="$node" />',
            [
                'node' => $group,
            ]
        );

        // Assert

        $view->assertDontSee('cn-navigation-children', false);
    }
}
