<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Bootstrap;

use App\Core\Navigation\Bootstrap\NavigationManifestBootstrapper;
use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

final class NavigationManifestBootstrapperTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_executes_manifest_discovery_on_boot(): void
    {
        $discovery = Mockery::mock(
            NavigationManifestDiscoveryInterface::class
        );

        $discovery
            ->shouldReceive('discover')
            ->once();

        $bootstrapper = new NavigationManifestBootstrapper(
            $discovery
        );

        $bootstrapper->boot();

        $this->assertTrue(true);
    }
}
