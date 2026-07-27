<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use Tests\TestCase;

use Illuminate\Support\Facades\Route;

use App\Core\Navigation\Resolver\NavigationActiveResolver;
use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;


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
            type: 'ITEM',
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


        Route::shouldReceive('currentRouteNamed')
            ->with('institutions')
            ->andReturn(false);



        $child = new NavigationNodeData(
            id: 'institutions.index',
            label: 'Instituciones',
            type: 'ITEM',
            route: 'institutions.index'
        );


        $parent = new NavigationNodeData(
            id: 'administration',
            label: 'Administración',
            type: 'GROUP',
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
            type: 'ITEM',
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
            type: 'GROUP',
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
}
