<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use Tests\TestCase;

use App\Core\Navigation\Registry\NavigationRegistry;

use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationTreeData;


final class NavigationRegistryTest extends TestCase
{

    public function test_register_group_stores_group(): void
    {
        $registry = new NavigationRegistry();


        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: null,
            order: 10,
        );


        $registry->registerGroup(
            $group
        );


        $groups = $registry->groups();


        $this->assertCount(
            1,
            $groups
        );


        $this->assertSame(
            $group,
            $groups['administration']
        );
    }



    public function test_register_item_stores_item(): void
    {
        $registry = new NavigationRegistry();


        $item = new NavigationItemData(
            id: 'institutions',
            label: 'Instituciones',
            route: 'institutions.index',
            group: 'administration',
        );


        $registry->registerItem(
            $item
        );


        $items = $registry->items();


        $this->assertCount(
            1,
            $items
        );


        $this->assertSame(
            $item,
            $items['institutions']
        );
    }



    public function test_tree_returns_empty_tree_when_not_defined(): void
    {
        $registry = new NavigationRegistry();


        $tree = $registry->tree();


        $this->assertInstanceOf(
            NavigationTreeData::class,
            $tree
        );


        $this->assertTrue(
            $tree->isEmpty()
        );
    }



    public function test_set_tree_stores_navigation_tree(): void
    {
        $registry = new NavigationRegistry();


        $tree = new NavigationTreeData();


        $registry->setTree(
            $tree
        );


        $this->assertSame(
            $tree,
            $registry->tree()
        );
    }



    public function test_clear_removes_navigation_state(): void
    {
        $registry = new NavigationRegistry();


        $registry->registerGroup(
            new NavigationGroupData(
                id: 'administration',
                label: 'Administración',
                icon: null,
                order: 10,
            )
        );


        $registry->registerItem(
            new NavigationItemData(
                id: 'institutions',
                label: 'Instituciones',
                route: 'institutions.index',
                group: 'administration',
            )
        );


        $registry->setTree(
            new NavigationTreeData()
        );


        $registry->clear();


        $this->assertEmpty(
            $registry->groups()
        );


        $this->assertEmpty(
            $registry->items()
        );


        $this->assertTrue(
            $registry->tree()->isEmpty()
        );
    }

}
