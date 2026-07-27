<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use Tests\TestCase;

use Mockery;

use App\Core\Navigation\Builder\NavigationBuilder;

use App\Core\Navigation\Contracts\NavigationRegistryInterface;

use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationTreeData;


final class NavigationBuilderTest extends TestCase
{

    public function test_build_returns_empty_tree_when_registry_is_empty(): void
    {
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
            $registry
        );


        $tree = $builder->build();


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
                'administration' => $group
            ]);


        $registry
            ->shouldReceive('items')
            ->once()
            ->andReturn([
                'institutions' => $item
            ]);



        $tree = (new NavigationBuilder(
            $registry
        ))->build();


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
            'GROUP',
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
            'ITEM',
            $itemNode->type()
        );


        $this->assertSame(
            'institutions.index',
            $itemNode->route()
        );
    }
}
