<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use App\Core\Security\Contracts\IdentityInterface;
use Mockery;
use Tests\TestCase;

final class NavigationBuilderTest extends TestCase
{
    public function test_build_returns_empty_tree_when_registry_is_empty(): void
    {
        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([]);

        $builder = new NavigationBuilder(
            $registry,
            $permissionResolver,
        );

        $tree = $builder->build(
            $identity
        );

        $this->assertInstanceOf(
            NavigationTreeData::class,
            $tree
        );

        $this->assertTrue(
            $tree->isEmpty()
        );
    }


    public function test_build_creates_group_with_items(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi-gear',
            order: 10,
        );

        $item = new NavigationItemData(
            id: 'institutions',
            label: 'Instituciones',
            route: 'institutions.index',
            icon: 'bi-building',
            order: 10,
            group: 'administration',
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([
                'administration' => $group,
            ]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'institutions' => $item,
            ]);

        $permissionResolver
            ->shouldReceive('canView')
            ->once()
            ->with(
                $identity,
                $item->permission()
            )
            ->andReturn(true);

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build(
            $identity
        );

        $this->assertInstanceOf(
            NavigationTreeData::class,
            $tree
        );

        $this->assertCount(
            1,
            $tree->nodes()
        );

        $groupNode = $tree->nodes()[0];

        $this->assertSame(
            'administration',
            $groupNode->id()
        );

        $this->assertSame(
            NavigationNodeType::GROUP,
            $groupNode->type()
        );

        $this->assertCount(
            1,
            $groupNode->children()
        );

        $itemNode = $groupNode->children()[0];

        $this->assertSame(
            'institutions',
            $itemNode->id()
        );

        $this->assertSame(
            NavigationNodeType::ITEM,
            $itemNode->type()
        );

        $this->assertSame(
            'institutions.index',
            $itemNode->route()
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_build_omits_items_when_permission_is_denied(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi-gear',
            order: 10,
        );

        $allowedItem = new NavigationItemData(
            id: 'users',
            label: 'Usuarios',
            route: 'users.index',
            icon: 'bi-people',
            order: 10,
            group: 'administration',
            permission: 'users.view',
        );

        $deniedItem = new NavigationItemData(
            id: 'roles',
            label: 'Roles',
            route: 'roles.index',
            icon: 'bi-shield',
            order: 20,
            group: 'administration',
            permission: 'roles.view',
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([
                'administration' => $group,
            ]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'users' => $allowedItem,
                'roles' => $deniedItem,
            ]);

        $permissionResolver
            ->shouldReceive('canView')
            ->once()
            ->with(
                $identity,
                $allowedItem->permission()
            )
            ->andReturn(true);

        $permissionResolver
            ->shouldReceive('canView')
            ->once()
            ->with(
                $identity,
                $deniedItem->permission()
            )
            ->andReturn(false);

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build($identity);

        $this->assertCount(
            1,
            $tree->nodes()
        );

        $groupNode = $tree->nodes()[0];

        $this->assertCount(
            1,
            $groupNode->children()
        );

        $this->assertSame(
            'users',
            $groupNode->children()[0]->id()
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_build_keeps_public_items_visible(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi-gear',
            order: 10,
        );

        $publicItem = new NavigationItemData(
            id: 'dashboard',
            label: 'Inicio',
            route: 'dashboard',
            icon: 'bi-house',
            order: 10,
            group: 'administration',
            permission: null,
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([
                'administration' => $group,
            ]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'dashboard' => $publicItem,
            ]);

        $permissionResolver
            ->shouldReceive('canView')
            ->once()
            ->with(
                $identity,
                null
            )
            ->andReturn(true);

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build($identity);

        $this->assertCount(
            1,
            $tree->nodes()
        );

        $groupNode = $tree->nodes()[0];

        $this->assertCount(
            1,
            $groupNode->children()
        );

        $itemNode = $groupNode->children()[0];

        $this->assertSame(
            'dashboard',
            $itemNode->id()
        );

        $this->assertSame(
            'dashboard',
            $itemNode->route()
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_build_removes_empty_groups(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi-gear',
            order: 10,
        );

        $users = new NavigationItemData(
            id: 'users',
            label: 'Usuarios',
            route: 'users.index',
            icon: 'bi-people',
            order: 10,
            group: 'administration',
            permission: 'users.view',
        );

        $roles = new NavigationItemData(
            id: 'roles',
            label: 'Roles',
            route: 'roles.index',
            icon: 'bi-shield',
            order: 20,
            group: 'administration',
            permission: 'roles.view',
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([
                'administration' => $group,
            ]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'users' => $users,
                'roles' => $roles,
            ]);

        $permissionResolver
            ->shouldReceive('canView')
            ->twice()
            ->andReturn(false);

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build($identity);

        $this->assertTrue(
            $tree->isEmpty()
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_build_preserves_group_and_item_order(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $group1 = new NavigationGroupData(
            id: 'group1',
            label: 'Group 1',
            icon: 'bi-gear',
            order: 20,
        );

        $group2 = new NavigationGroupData(
            id: 'group2',
            label: 'Group 2',
            icon: 'bi-gear',
            order: 10,
        );

        $item1 = new NavigationItemData(
            id: 'item1',
            label: 'Item 1',
            route: 'item1.index',
            icon: 'bi-building',
            order: 20,
            group: 'group1',
        );

        $item2 = new NavigationItemData(
            id: 'item2',
            label: 'Item 2',
            route: 'item2.index',
            icon: 'bi-building',
            order: 10,
            group: 'group1',
        );

        $item3 = new NavigationItemData(
            id: 'item3',
            label: 'Item 3',
            route: 'item3.index',
            icon: 'bi-building',
            order: 10,
            group: 'group2',
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([
                'group1' => $group1,
                'group2' => $group2,
            ]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'item1' => $item1,
                'item2' => $item2,
                'item3' => $item3,
            ]);

        $permissionResolver
            ->shouldReceive('canView')
            ->times(3)
            ->andReturn(true);

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build($identity);

        // Assert that groups are ordered by their order property
        $this->assertSame(
            ['group1', 'group2'],
            array_map(fn($node) => $node->id(), $tree->nodes())
        );

        // Assert that items within group1 are ordered by their order property
        $group1Node = collect($tree->nodes())->firstWhere(fn($node) => $node->id() === 'group1');

        $this->assertSame(
            ['item1', 'item2'],
            array_map(
                fn($node) => $node->id(),
                $group1Node->children()
            )
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_build_does_not_mutate_registry_data(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );

        $permissionResolver = Mockery::mock(
            NavigationPermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi-gear',
            order: 10,
        );

        $item = new NavigationItemData(
            id: 'users',
            label: 'Usuarios',
            route: 'users.index',
            icon: 'bi-people',
            order: 10,
            group: 'administration',
            permission: 'users.view',
        );

        $registry
            ->shouldReceive('groups')
            ->once()
            ->andReturn([
                'administration' => $group,
            ]);

        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'users' => $item,
            ]);

        $permissionResolver
            ->shouldReceive('canView')
            ->once()
            ->with(
                $identity,
                $item->permission()
            )
            ->andReturn(true);

        $tree = (new NavigationBuilder(
            $registry,
            $permissionResolver,
        ))->build($identity);

        // Assert that the original registry data is not mutated
        $this->assertSame(
            'administration',
            $group->id()
        );

        $this->assertSame(
            'users',
            $item->id()
        );
    }
}
