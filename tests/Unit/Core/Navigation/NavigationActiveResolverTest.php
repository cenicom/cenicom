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


}
