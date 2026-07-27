<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Navigation;


use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use Tests\TestCase;

final class FullNavigationRenderTest extends TestCase
{
    public function test_renders_complete_navigation_tree(): void
    {
        // Arrange

        $institutions = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: 'ITEM',
        );

        $users = new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: 'ITEM',
        );

        $group = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: 'GROUP',
            children: [
                $institutions,
                $users,
            ],
        );

        $navigation = new NavigationTreeData(
            nodes: [
                $group,
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

        $view->assertSee('Administración');
        $view->assertSee('Instituciones');
        $view->assertSee('Usuarios');
    }

    public function test_renders_multiple_groups(): void
    {
        // Arrange

        $administration = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: 'GROUP',
        );

        $configuration = new NavigationNodeData(
            id: 'configuration',
            label: 'Configuración',
            type: 'GROUP',
        );

        $navigation = new NavigationTreeData(
            nodes: [
                $administration,
                $configuration,
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

        $view->assertSee('Administración');
        $view->assertSee('Configuración');
    }

    public function test_renders_nested_navigation_correctly(): void
    {
        // Arrange

        $child = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: 'ITEM',
        );

        $group = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: 'GROUP',
            children: [
                $child,
            ],
        );

        $navigation = new NavigationTreeData(
            nodes: [
                $group,
            ],
        );

        // Act

        $view = $this->blade(
            '<x-cn.navigation.sidebar :navigation="$navigation" />',
            [
                'navigation' => $navigation,
            ]
        );

        // Assert

        $view->assertSee('Administración');
        $view->assertSee('Instituciones');
        $view->assertSee('cn-navigation-children', false);
    }
}
