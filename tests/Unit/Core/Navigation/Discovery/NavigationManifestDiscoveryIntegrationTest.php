<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;
use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;
use App\Core\Navigation\Discovery\NavigationManifestDiscoveryService;
use App\Core\Navigation\DTO\NavigationManifestData;
use Mockery;
use PHPUnit\Framework\TestCase;

final class NavigationManifestDiscoveryIntegrationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_executes_complete_discovery_pipeline(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */

        $manifestA = new NavigationManifestData(
            module: 'Users',
        );

        $manifestB = new NavigationManifestData(
            module: 'Inventory',
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

        $pathA = '/modules/Users/navigation.php';
        $pathB = '/modules/Inventory/navigation.php';

        $finder
            ->shouldReceive('discover')
            ->once()
            ->andReturn([
                $pathA,
                $pathB,
            ]);

        $loader
            ->shouldReceive('load')
            ->once()
            ->with($pathA)
            ->andReturn($manifestA);

        $loader
            ->shouldReceive('load')
            ->once()
            ->with($pathB)
            ->andReturn($manifestB);

        $registrar
            ->shouldReceive('register')
            ->once()
            ->with($manifestA);

        $registrar
            ->shouldReceive('register')
            ->once()
            ->with($manifestB);

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

        $this->assertInstanceOf(
            NavigationManifestDiscoveryInterface::class,
            $service
        );
    }

    public function test_handles_empty_discovery_result(): void
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

        $this->assertTrue(true);
    }
}
