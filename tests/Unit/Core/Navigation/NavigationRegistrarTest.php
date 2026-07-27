<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use Tests\TestCase;

use Mockery;

use App\Core\Navigation\Registrar\NavigationRegistrar;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;


final class NavigationRegistrarTest extends TestCase
{

    public function test_register_group_delegates_to_registry(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );


        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: null,
            order: 10,
        );


        $registry
            ->shouldReceive('registerGroup')
            ->once()
            ->with($group);


        $registrar = new NavigationRegistrar(
            $registry
        );


        $result = $registrar->group(
            $group
        );


        $this->assertSame(
            $registrar,
            $result
        );
    }



    public function test_register_item_delegates_to_registry(): void
    {
        $registry = Mockery::mock(
            NavigationRegistryInterface::class
        );


        $item = new NavigationItemData(
            id: 'institutions',
            label: 'Instituciones',
            route: 'institutions.index',
            icon: null,
            order: 10,
            group: 'administration',
        );


        $registry
            ->shouldReceive('registerItem')
            ->once()
            ->with($item);


        $registrar = new NavigationRegistrar(
            $registry
        );


        $result = $registrar->item(
            $item
        );


        $this->assertSame(
            $registrar,
            $result
        );
    }


}
