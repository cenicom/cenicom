<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Discovery\Contracts\NavigationDiscoveryInterface;
use App\Core\Navigation\Discovery\NavigationDiscoveryRegistrar;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationManifestData;
use PHPUnit\Framework\TestCase;

final class NavigationDiscoveryRegistrarTest extends TestCase
{
    public function test_registers_manifest_groups_and_items(): void
    {
        $path = '/modules/Institution/navigation.php';

        $group = new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'bi bi-gear',
            order: 10,
        );

        $item = new NavigationItemData(
            id: 'institutions.index',
            group: 'administration',
            label: 'Instituciones',
            route: 'institutions.index',
            icon: 'bi bi-building',
            order: 10,
        );

        $manifest = new NavigationManifestData(
            module: 'Institution',
            groups: [$group],
            items: [$item],
        );

        $discovery = $this->createMock(
            NavigationDiscoveryInterface::class
        );

        $discovery
            ->expects($this->once())
            ->method('discover')
            ->willReturn([$path]);

        $loader = $this->createMock(
            NavigationManifestLoaderInterface::class
        );

        $loader
            ->expects($this->once())
            ->method('load')
            ->with($path)
            ->willReturn($manifest);

        $registrar = $this->createMock(
            NavigationRegistrarInterface::class
        );

        $registrar
            ->expects($this->once())
            ->method('group')
            ->with($group)
            ->willReturnSelf();

        $registrar
            ->expects($this->once())
            ->method('item')
            ->with($item)
            ->willReturnSelf();

        $service = new NavigationDiscoveryRegistrar(
            $discovery,
            $loader,
            $registrar,
        );

        $service->register();
    }

    public function test_does_nothing_when_no_manifests_are_found(): void
    {
        $discovery = $this->createMock(
            NavigationDiscoveryInterface::class
        );

        $discovery
            ->expects($this->once())
            ->method('discover')
            ->willReturn([]);

        $loader = $this->createMock(
            NavigationManifestLoaderInterface::class
        );

        $loader
            ->expects($this->never())
            ->method('load');

        $registrar = $this->createMock(
            NavigationRegistrarInterface::class
        );

        $registrar
            ->expects($this->never())
            ->method('group');

        $registrar
            ->expects($this->never())
            ->method('item');

        $service = new NavigationDiscoveryRegistrar(
            $discovery,
            $loader,
            $registrar,
        );

        $service->register();
    }
}
