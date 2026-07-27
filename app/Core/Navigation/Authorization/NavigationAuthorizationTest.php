<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Authorization;

use App\Core\Navigation\Authorization\NavigationAuthorization;
use App\Core\Navigation\DTO\NavigationNodeData;
use PHPUnit\Framework\TestCase;

final class NavigationAuthorizationTest extends TestCase
{
    public function test_allows_public_node(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $authorization = new NavigationAuthorization();

        $node = $this->makeItem();

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $result = $authorization->allows(
            $node
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $result
        );
    }

    public function test_returns_boolean(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $authorization = new NavigationAuthorization();

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $result = $authorization->allows(
            $this->makeItem()
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->assertIsBool(
            $result
        );
    }

    public function test_allows_group_node(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $authorization = new NavigationAuthorization();

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $result = $authorization->allows(
            $this->makeGroup()
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->assertTrue(
            $result
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function makeItem(): NavigationNodeData
    {
        return new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: 'ITEM',
            icon: 'bi-people',
            route: 'users.index',
            order: 1,
            children: [],
            url: null,
            routeParameters: [],
        );
    }

    private function makeGroup(): NavigationNodeData
    {
        return new NavigationNodeData(
            id: 'system',
            label: 'Sistema',
            type: 'GROUP',
            icon: 'bi-gear',
            route: null,
            order: 1,
            children: [],
            url: null,
            routeParameters: [],
        );
    }
}
