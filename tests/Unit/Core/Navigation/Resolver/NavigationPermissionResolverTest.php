<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Resolver;

use App\Core\Contracts\NavigationAuthorizationInterface;
use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use App\Core\Navigation\Resolver\NavigationPermissionResolver;
use PHPUnit\Framework\TestCase;

final class NavigationPermissionResolverTest extends TestCase
{
    public function test_returns_empty_tree(): void
    {
        // Arrange

        $resolver = new NavigationPermissionResolver(
            $this->authorization(true)
        );

        $tree = new NavigationTreeData(
            nodes: [],
        );

        // Act

        $result = $resolver->resolve($tree);

        // Assert

        $this->assertEmpty(
            $result->nodes()
        );
    }

    public function test_keeps_authorized_item(): void
    {
        // Arrange

        $resolver = new NavigationPermissionResolver(
            $this->authorization(true)
        );

        $tree = new NavigationTreeData([
            $this->makeItem(),
        ]);

        // Act

        $result = $resolver->resolve($tree);

        // Assert

        $this->assertCount(
            1,
            $result->nodes()
        );
    }

    public function test_removes_unauthorized_item(): void
    {
        // Arrange

        $resolver = new NavigationPermissionResolver(
            $this->authorization(false)
        );

        $tree = new NavigationTreeData([
            $this->makeItem(),
        ]);

        // Act

        $result = $resolver->resolve($tree);

        // Assert

        $this->assertEmpty(
            $result->nodes()
        );
    }

    public function test_keeps_group_with_authorized_children(): void
    {
        // Arrange

        $resolver = new NavigationPermissionResolver(
            $this->authorization(true)
        );

        $group = $this->makeGroup([
            $this->makeItem(),
        ]);

        $tree = new NavigationTreeData([
            $group,
        ]);

        // Act

        $result = $resolver->resolve($tree);

        // Assert

        $this->assertCount(
            1,
            $result->nodes()
        );

        $this->assertCount(
            1,
            $result->nodes()[0]->children()
        );
    }

    public function test_removes_empty_group(): void
    {
        // Arrange

        $resolver = new NavigationPermissionResolver(
            $this->authorization(false)
        );

        $group = $this->makeGroup([
            $this->makeItem(),
        ]);

        $tree = new NavigationTreeData([
            $group,
        ]);

        // Act

        $result = $resolver->resolve($tree);

        // Assert

        $this->assertEmpty(
            $result->nodes()
        );
    }

    public function test_returns_navigation_tree(): void
    {
        $resolver = new NavigationPermissionResolver(
            $this->authorization(true)
        );

        $result = $resolver->resolve(
            new NavigationTreeData([])
        );

        $this->assertInstanceOf(
            NavigationTreeData::class,
            $result
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function authorization(
        bool $allowed
    ): NavigationAuthorizationInterface {

        $mock = $this->createMock(
            NavigationAuthorizationInterface::class
        );

        $mock
            ->method('allows')
            ->willReturn($allowed);

        return $mock;
    }

    private function makeItem(): NavigationNodeData
    {
        return new NavigationNodeData(
            id: 'users',
            label: 'Usuarios',
            type: NavigationNodeType::ITEM,
            icon: 'bi-people',
            route: 'users.index',
            order: 1,
            children: [],
            url: null,
            routeParameters: [],
        );
    }

    /**
     * @param array<int, NavigationNodeData> $children
     */
    private function makeGroup(
        array $children
    ): NavigationNodeData {

        return new NavigationNodeData(
            id: 'system',
            label: 'Sistema',
            type: NavigationNodeType::GROUP,
            icon: 'bi-gear',
            route: null,
            order: 1,
            children: $children,
            url: null,
            routeParameters: [],
        );
    }
}
