<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Inventory\Navigation;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Modules\Inventory\Navigation\InventoryNavigation;
use Mockery;
use Tests\TestCase;

final class InventoryNavigationTest extends TestCase
{
    public function test_registers_inventory_navigation(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once();

        $navigation
            ->shouldReceive('item')
            ->times(3);


        // Act

        $definition = new InventoryNavigation();

        $definition->register(
            $navigation
        );


        // Assert

        $this->assertTrue(true);
    }


    public function test_registers_inventory_group(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once()
            ->withArgs(function (
                NavigationGroupData $group
            ) {

                return
                    $group->id() === 'inventory'
                    &&
                    $group->label() === 'Inventario';
            });


        $navigation
            ->shouldReceive('item')
            ->times(3);


        // Act

        $definition = new InventoryNavigation();

        $definition->register(
            $navigation
        );
    }


    public function test_registers_products_item(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once();


        $navigation
            ->shouldReceive('item')
            ->withArgs(function (
                NavigationItemData $item
            ) {

                return
                    $item->id() === 'products'
                    &&
                    $item->label() === 'Productos'
                    &&
                    $item->route() === 'products.index';
            })
            ->once();


        $navigation
            ->shouldReceive('item')
            ->twice();


        // Act

        $definition = new InventoryNavigation();

        $definition->register(
            $navigation
        );
    }


    public function test_registers_categories_item(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once();


        $navigation
            ->shouldReceive('item')
            ->withArgs(function (
                NavigationItemData $item
            ) {

                return
                    $item->id() === 'categories'
                    &&
                    $item->label() === 'Categorías'
                    &&
                    $item->route() === 'categories.index';
            })
            ->once();


        $navigation
            ->shouldReceive('item')
            ->twice();


        // Act

        $definition = new InventoryNavigation();

        $definition->register(
            $navigation
        );
    }


    public function test_registers_movements_item(): void
    {
        // Arrange

        $navigation = Mockery::mock(
            NavigationRegistrarInterface::class
        );


        $navigation
            ->shouldReceive('group')
            ->once();


        $navigation
            ->shouldReceive('item')
            ->withArgs(function (
                NavigationItemData $item
            ) {

                return
                    $item->id() === 'movements'
                    &&
                    $item->label() === 'Movimientos'
                    &&
                    $item->route() === 'movements.index';
            })
            ->once();


        $navigation
            ->shouldReceive('item')
            ->twice();


        // Act

        $definition = new InventoryNavigation();

        $definition->register(
            $navigation
        );
    }
}
