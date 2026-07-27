<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Registrar;

use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\Registrar\NavigationRegistrar;
use PHPUnit\Framework\TestCase;

final class NavigationRegistrarTest extends TestCase
{
    public function test_delegates_group_registration(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $group = $this->createMock(
            NavigationGroupData::class
        );

        $registry
            ->expects($this->once())
            ->method('registerGroup')
            ->with($group);

        $registrar = new NavigationRegistrar(
            $registry
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $registrar->group($group);
    }

    public function test_delegates_item_registration(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $item = $this->createMock(
            NavigationItemData::class
        );

        $registry
            ->expects($this->once())
            ->method('registerItem')
            ->with($item);

        $registrar = new NavigationRegistrar(
            $registry
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $registrar->item($item);
    }

    public function test_group_returns_self(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $group = $this->createMock(
            NavigationGroupData::class
        );

        $registry
            ->expects($this->once())
            ->method('registerGroup');

        $registrar = new NavigationRegistrar(
            $registry
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $result = $registrar->group(
            $group
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->assertSame(
            $registrar,
            $result
        );
    }

    public function test_item_returns_self(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $registry = $this->createMock(
            NavigationRegistryInterface::class
        );

        $item = $this->createMock(
            NavigationItemData::class
        );

        $registry
            ->expects($this->once())
            ->method('registerItem');

        $registrar = new NavigationRegistrar(
            $registry
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $result = $registrar->item(
            $item
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->assertSame(
            $registrar,
            $result
        );
    }
}
