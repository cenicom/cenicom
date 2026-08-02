<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use App\Core\Navigation\Resolver\NavigationActiveResolver;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;


final class NavigationActiveResolverTest extends TestCase
{

    public function test_marks_current_node_when_route_matches(): void
    {
        Route::shouldReceive('currentRouteNamed')
            ->once()
            ->with('institutions.index')
            ->andReturn(true);


        $node = new NavigationNodeData(
            id: 'institutions.index',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
            route: 'institutions.index'
        );


        $tree = new NavigationTreeData([
            $node
        ]);


        $result = (new NavigationActiveResolver())
            ->resolve($tree);


        $resolved = $result->nodes()[0];


        $this->assertTrue(
            $resolved->isCurrent()
        );

        $this->assertTrue(
            $resolved->isActive()
        );

        $this->assertTrue(
            $resolved->isExpanded()
        );
    }


    public function test_marks_parent_as_ancestor_when_child_is_current(): void
    {

        Route::shouldReceive('currentRouteNamed')
            ->with('institutions.index')
            ->andReturn(true);


        $child = new NavigationNodeData(
            id: 'institutions.index',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
            route: 'institutions.index'
        );


        $parent = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            route: null,
            children: [
                $child
            ]
        );


        $tree = new NavigationTreeData([
            $parent
        ]);


        $result = (new NavigationActiveResolver())
            ->resolve($tree);


        $resolvedParent = $result->nodes()[0];


        $this->assertTrue(
            $resolvedParent->isAncestor()
        );


        $this->assertTrue(
            $resolvedParent->isActive()
        );


        $this->assertTrue(
            $resolvedParent->isExpanded()
        );
    }


    public function test_node_without_matching_route_is_inactive(): void
    {

        Route::shouldReceive('currentRouteNamed')
            ->with('users.index')
            ->andReturn(false);


        $node = new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: NavigationNodeType::ITEM,
            route: 'users.index'
        );



        $tree = new NavigationTreeData([
            $node
        ]);


        $result = (new NavigationActiveResolver())
            ->resolve($tree);


        $resolved = $result
            ->nodes()[0];


        $this->assertFalse(
            $resolved->isCurrent()
        );


        $this->assertFalse(
            $resolved->isActive()
        );


        $this->assertFalse(
            $resolved->isExpanded()
        );
    }


    public function test_node_without_route_is_ignored(): void
    {

        $node = new NavigationNodeData(
            id: 'configuration',
            label: 'Configuración',
            type: NavigationNodeType::GROUP,
            route: null
        );


        $tree = new NavigationTreeData([
            $node
        ]);


        $result = (new NavigationActiveResolver())
            ->resolve($tree);


        $resolved = $result->nodes()[0];


        $this->assertFalse(
            $resolved->isCurrent()
        );


        $this->assertFalse(
            $resolved->isActive()
        );
    }

    /* Cobertura que aún falta
     * Para certificar completamente CN-NAV-003.2, añadiría estos cinco casos. */

    //1. Árbol vacío -- Objetivo: garantizar que el resolver no falle con un árbol vacío.
    public function test_resolve_returns_empty_tree_when_tree_is_empty(): void
    {
        $tree = new NavigationTreeData([]);

        $result = (new NavigationActiveResolver())->resolve($tree);

        $this->assertTrue($result->isEmpty());
    }

    //2. Inmutabilidad -- Este es probablemente el test más importante de todo el sprint.
    public function test_resolve_does_not_modify_original_tree(): void
    {
        Route::shouldReceive('currentRouteNamed')
            ->once()
            ->with('institutions.index')
            ->andReturn(true);

        $node = new NavigationNodeData(
            id: 'institutions',
            label: 'Instituciones',
            type: NavigationNodeType::ITEM,
            route: 'institutions.index'
        );

        $tree = new NavigationTreeData([$node]);

        $resolved = (new NavigationActiveResolver())->resolve($tree);

        $this->assertNotSame($tree, $resolved);

        $this->assertFalse(
            $tree->nodes()[0]->isActive()
        );

        $this->assertTrue(
            $resolved->nodes()[0]->isActive()
        );
    }

    public function test_marks_all_ancestors_in_multilevel_tree(): void
    {
        Route::shouldReceive('currentRouteNamed')
            ->with('permissions.index')
            ->andReturn(true);

        $item = new NavigationNodeData(
            id: 'permissions.index',
            label: 'Permisos',
            type: NavigationNodeType::ITEM,
            route: 'permissions.index'
        );

        $section = new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: NavigationNodeType::GROUP,
            children: [
                $item
            ]
        );

        $root = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            children: [
                $section
            ]
        );

        $tree = new NavigationTreeData([
            $root
        ]);

        $result = (new NavigationActiveResolver())
            ->resolve($tree);

        $resolvedRoot = $result->nodes()[0];
        $resolvedSection = $resolvedRoot->children()[0];
        $resolvedItem = $resolvedSection->children()[0];

        $this->assertTrue(
            $resolvedRoot->isAncestor()
        );

        $this->assertTrue(
            $resolvedRoot->isExpanded()
        );

        $this->assertTrue(
            $resolvedSection->isAncestor()
        );

        $this->assertTrue(
            $resolvedSection->isExpanded()
        );

        $this->assertTrue(
            $resolvedItem->isCurrent()
        );

        $this->assertTrue(
            $resolvedItem->isActive()
        );
    }


    public function test_does_not_activate_unrelated_branch(): void
    {
        Route::shouldReceive('currentRouteNamed')
            ->with('products.index')
            ->andReturn(false);

        Route::shouldReceive('currentRouteNamed')
            ->with('users.index')
            ->andReturn(true);

        $users = new NavigationNodeData(
            id: 'users.index',
            label: 'Usuarios',
            type: NavigationNodeType::ITEM,
            route: 'users.index'
        );

        $products = new NavigationNodeData(
            id: 'products.index',
            label: 'Productos',
            type: NavigationNodeType::ITEM,
            route: 'products.index'
        );

        $administration = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            children: [
                $users
            ]
        );

        $inventory = new NavigationNodeData(
            id: 'inventory',
            label: 'Inventario',
            type: NavigationNodeType::GROUP,
            children: [
                $products
            ]
        );

        $tree = new NavigationTreeData([
            $administration,
            $inventory,
        ]);

        $result = (new NavigationActiveResolver())
            ->resolve($tree);

        $resolvedAdministration = $result->nodes()[0];
        $resolvedInventory = $result->nodes()[1];

        $this->assertTrue(
            $resolvedAdministration->isActive()
        );

        $this->assertFalse(
            $resolvedInventory->isActive()
        );
    }


    public function test_group_without_active_child_remains_inactive(): void
    {
        Route::shouldReceive('currentRouteNamed')
            ->with('users.index')
            ->andReturn(false);

        $child = new NavigationNodeData(
            id: 'users.index',
            label: 'Usuarios',
            type: NavigationNodeType::ITEM,
            route: 'users.index'
        );

        $group = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: NavigationNodeType::GROUP,
            children: [
                $child
            ]
        );

        $tree = new NavigationTreeData([
            $group
        ]);

        $result = (new NavigationActiveResolver())
            ->resolve($tree);

        $resolved = $result->nodes()[0];

        $this->assertFalse(
            $resolved->isActive()
        );

        $this->assertFalse(
            $resolved->isAncestor()
        );

        $this->assertFalse(
            $resolved->isExpanded()
        );
    }
}
