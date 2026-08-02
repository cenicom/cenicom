<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Discovery\NavigationManifestRegistrar;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationManifestData;
use Mockery;
use PHPUnit\Framework\TestCase;

final class NavigationManifestRegistrarTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_registers_groups_and_items(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $group = new NavigationGroupData(
            id: 'system',
            label: 'Sistema',
            icon: 'bi-gear',
            order: 10,
        );

        $item = new NavigationItemData(
            id: 'users',
            group: 'system',
            label: 'Usuarios',
            route: 'users.index',
            icon: 'bi-people',
            order: 1,
        );

        $manifest = new NavigationManifestData(
            module: 'Users',
            groups: [$group],
            items: [$item],
        );

        $registrar
            ->shouldReceive('group')
            ->once()
            ->with($group);

        $registrar
            ->shouldReceive('item')
            ->once()
            ->with($item);

        $loader = new NavigationManifestRegistrar(
            $registrar
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $loader->register(
            $manifest
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->addToAssertionCount(1);
    }

    public function test_registers_empty_manifest(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $registrar = Mockery::mock(
            NavigationRegistrarInterface::class
        );

        $registrar
            ->shouldNotReceive('group');

        $registrar
            ->shouldNotReceive('item');

        $manifest = new NavigationManifestData(
            module: 'Empty'
        );

        $loader = new NavigationManifestRegistrar(
            $registrar
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $loader->register(
            $manifest
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->addToAssertionCount(1);
    }
}
