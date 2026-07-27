<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Builder;

use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use PHPUnit\Framework\TestCase;

final class NavigationBuilderTest extends TestCase
{
    public function test_builds_empty_tree(): void
    {
        // Arrange

        $registry = $this->createRegistry([], []);

        $builder = new NavigationBuilder($registry);

        // Act

        $tree = $builder->build();

        // Assert

        $this->assertEmpty(
            $tree->nodes()
        );
    }

    public function test_builds_tree_with_single_group(): void
    {
        // Arrange

        $group = $this->makeGroup();

        $registry = $this->createRegistry(
            [$group],
            []
        );

        $builder = new NavigationBuilder($registry);

        // Act

        $tree = $builder->build();

        // Assert

        $this->assertCount(
            1,
            $tree->nodes()
        );

        $this->assertSame(
            'system',
            $tree->nodes()[0]->id()
        );
    }

    public function test_builds_tree_with_group_and_items(): void
    {
        // Arrange

        $group = $this->makeGroup();

        $items = [

            $this->makeItem(
                'users',
                'system'
            ),

            $this->makeItem(
                'roles',
                'system'
            ),

        ];

        $registry = $this->createRegistry(
            [$group],
            $items
        );

        $builder = new NavigationBuilder($registry);

        // Act

        $tree = $builder->build();

        // Assert

        $this->assertCount(
            2,
            $tree->nodes()[0]->children()
        );
    }

    public function test_ignores_items_from_other_groups(): void
    {
        // ...
    }

    public function test_preserves_group_order(): void
    {
        // ...
    }

    public function test_preserves_item_order(): void
    {
        // ...
    }

    public function test_build_returns_navigation_tree(): void
    {
        // ...
    }

    // -------------------------------------------------

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
        ?string $icon = 'bi-circle',
        int $order = 1,
    ): NavigationItemData {

        return new NavigationItemData(
            id: $id,
            label: $label !== '' ? $label : ucfirst($id),
            route: $id . '.index',
            icon: $icon,
            order: $order,
            group: $group,
        );
    }
}
