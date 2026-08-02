<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;
use App\Core\Navigation\Discovery\NavigationManifestDiscoveryService;
use App\Core\Navigation\DTO\NavigationManifestData;
use Mockery;
use PHPUnit\Framework\TestCase;

final class NavigationManifestDiscoveryServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_discovers_and_registers_navigation_manifests(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $manifest = new NavigationManifestData(
            module: 'Users',
        );

        $finder = Mockery::mock(
            NavigationManifestFinderInterface::class
        );

        $loader = Mockery::mock(
            NavigationManifestLoaderInterface::class
        );

        $registrar = Mockery::mock(
            NavigationManifestRegistrarInterface::class
        );

        $finder
            ->shouldReceive('discover')
            ->once()
            ->andReturn([$manifest]);

        $loader
            ->shouldReceive('load')
            ->once()
            ->with($manifest)
            ->andReturn($manifest);

        $registrar
            ->shouldReceive('register')
            ->once()
            ->with($manifest);

        $service = new NavigationManifestDiscoveryService(
            $finder,
            $loader,
            $registrar,
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $service->discover();

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->addToAssertionCount(1);
    }

    public function test_handles_empty_manifest_collection(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $finder = Mockery::mock(
            NavigationManifestFinderInterface::class
        );

        $loader = Mockery::mock(
            NavigationManifestLoaderInterface::class
        );

        $registrar = Mockery::mock(
            NavigationManifestRegistrarInterface::class
        );

        $finder
            ->shouldReceive('discover')
            ->once()
            ->andReturn([]);

        $loader
            ->shouldNotReceive('load');

        $registrar
            ->shouldNotReceive('register');

        $service = new NavigationManifestDiscoveryService(
            $finder,
            $loader,
            $registrar,
        );

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $service->discover();

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->addToAssertionCount(1);
    }
}
