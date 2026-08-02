<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Builder;

use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Security\Contracts\IdentityInterface;
use PHPUnit\Framework\TestCase;

final class NavigationBuilderTest extends TestCase
{
    public function test_builds_empty_tree(): void
    {
        $registry = $this->createRegistry([], []);

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertEmpty(
            $tree->nodes()
        );
    }


    public function test_omits_group_without_visible_items(): void
    {
        $group = $this->makeGroup();

        $registry = $this->createRegistry(
            [$group],
            []
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertCount(
            0,
            $tree->nodes()
        );

        $this->assertEmpty(
            $tree->nodes()
        );
    }


    public function test_builds_tree_with_group_and_items(): void
    {
        $group = $this->makeGroup();

        $items = [
            $this->makeItem('users', 'system'),
            $this->makeItem('roles', 'system'),
        ];

        $registry = $this->createRegistry(
            [$group],
            $items
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertCount(
            2,
            $tree->nodes()[0]->children()
        );
    }


    public function test_builds_tree_when_all_permissions_are_granted(): void
    {
        $groupAcademic = $this->makeGroup(
            id: 'academic',
            label: 'Académico',
            order: 1,
        );

        $groupAdministration = $this->makeGroup(
            id: 'administration',
            label: 'Administración',
            order: 2,
        );

        $items = [
            $this->makeItem(
                id: 'courses',
                group: 'academic',
                permission: 'courses.view',
            ),

            $this->makeItem(
                id: 'students',
                group: 'academic',
                permission: 'students.view',
            ),

            $this->makeItem(
                id: 'users',
                group: 'administration',
                permission: 'users.view',
            ),

            $this->makeItem(
                id: 'roles',
                group: 'administration',
                permission: 'roles.view',
            ),
        ];

        $registry = $this->createRegistry(
            [
                $groupAcademic,
                $groupAdministration,
            ],
            $items
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertCount(
            2,
            $tree->nodes()
        );
    }


    public function test_omits_items_when_permission_is_denied(): void
    {
        $group = $this->makeGroup();

        $items = [
            $this->makeItem(
                id: 'dashboard',
                group: 'system',
                permission: null,
            ),

            $this->makeItem(
                id: 'users',
                group: 'system',
                permission: 'users.view',
            ),

            $this->makeItem(
                id: 'roles',
                group: 'system',
                permission: 'roles.view',
            ),
        ];

        $registry = $this->createRegistry(
            [$group],
            $items
        );

        [$builder, $identity] = $this->createBuilder(
            $registry,
            [
                'users.view' => false,
            ]
        );

        $tree = $builder->build($identity);

        $children = $tree->nodes()[0]->children();

        $this->assertCount(
            2,
            $children
        );
    }


    public function test_keeps_public_items_visible(): void
    {
        $group = $this->makeGroup();

        $registry = $this->createRegistry(
            [$group],
            [
                $this->makeItem(
                    id: 'dashboard',
                    group: 'system',
                    permission: null,
                ),
            ]
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertCount(
            1,
            $tree->nodes()[0]->children()
        );
    }


    public function test_removes_group_when_all_items_are_denied(): void
    {
        $group = $this->makeGroup();

        $registry = $this->createRegistry(
            [$group],
            [
                $this->makeItem(
                    id: 'users',
                    group: 'system',
                    permission: 'users.view',
                ),
            ]
        );

        [$builder, $identity] = $this->createBuilder(
            $registry,
            [
                'users.view' => false,
            ]
        );

        $tree = $builder->build($identity);

        $this->assertEmpty(
            $tree->nodes()
        );
    }


    public function test_ignores_items_from_other_groups(): void
    {
        $registry = $this->createRegistry(
            [
                $this->makeGroup(),
            ],
            [
                $this->makeItem(
                    id: 'users',
                    group: 'system',
                ),
                $this->makeItem(
                    id: 'roles',
                    group: 'security',
                ),
            ]
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertCount(
            1,
            $tree->nodes()[0]->children()
        );
    }


    public function test_preserves_group_order(): void
    {
        $registry = $this->createRegistry(
            [
                $this->makeGroup(
                    id: 'second',
                    label: 'Segundo',
                ),

                $this->makeGroup(
                    id: 'first',
                    label: 'Primero',
                ),
            ],
            [
                $this->makeItem(
                    id: 'item2',
                    group: 'second',
                ),

                $this->makeItem(
                    id: 'item1',
                    group: 'first',
                ),
            ]
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertSame(
            'second',
            $tree->nodes()[0]->id()
        );

        $this->assertSame(
            'first',
            $tree->nodes()[1]->id()
        );
    }


    public function test_preserves_item_order(): void
    {
        $registry = $this->createRegistry(
            [
                $this->makeGroup(),
            ],
            [
                $this->makeItem('roles', 'system'),
                $this->makeItem('users', 'system'),
            ]
        );

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertSame(
            'roles',
            $tree->nodes()[0]->children()[0]->id()
        );
    }


    public function test_build_returns_navigation_tree(): void
    {
        $registry = $this->createRegistry([], []);

        [$builder, $identity] = $this->createBuilder($registry);

        $tree = $builder->build($identity);

        $this->assertInstanceOf(
            \App\Core\Navigation\DTO\NavigationTreeData::class,
            $tree
        );
    }


    private function createBuilder(
        NavigationRegistryInterface $registry,
        array $permissions = [],
    ): array {

        $permissionResolver = $this->createMock(
            NavigationPermissionResolverInterface::class
        );

        $permissionResolver
            ->method('canView')
            ->willReturnCallback(
                function (
                    IdentityInterface $identity,
                    ?string $permission
                ) use ($permissions): bool {

                    if ($permission === null) {
                        return true;
                    }

                    return $permissions[$permission] ?? true;
                }
            );

        $identity = $this->createMock(
            IdentityInterface::class
        );

        return [
            new NavigationBuilder(
                $registry,
                $permissionResolver,
            ),
            $identity,
        ];
    }


    private function createRegistry(
        array $groups,
        array $items
    ): NavigationRegistryInterface {

        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $registry
            ->method('groups')
            ->willReturn($groups);

        $registry
            ->method('items')
            ->willReturn($items);

        return $registry;
    }


    private function makeGroup(
        string $id = 'system',
        string $label = 'Sistema',
        ?string $icon = 'bi-gear',
        int $order = 1,
    ): NavigationGroupData {

        return new NavigationGroupData(
            id: $id,
            label: $label,
            icon: $icon,
            order: $order,
        );
    }


    private function makeItem(
        string $id,
        string $group,
        string $label = '',
        ?string $permission = null,
        ?string $icon = 'bi-circle',
        int $order = 1,
    ): NavigationItemData {

        return new NavigationItemData(
            id: $id,
            label: $label !== '' ? $label : ucfirst($id),
            route: $id . '.index',
            permission: $permission,
            icon: $icon,
            order: $order,
            group: $group,
        );
    }
}
