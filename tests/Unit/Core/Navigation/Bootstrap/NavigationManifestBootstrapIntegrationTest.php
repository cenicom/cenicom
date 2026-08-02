<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Bootstrap;

use App\Core\Navigation\Bootstrap\NavigationManifestBootstrapper;
use App\Core\Navigation\Contracts\NavigationManifestBootstrapperInterface;

use Tests\TestCase;

final class NavigationManifestBootstrapIntegrationTest extends TestCase
{
    public function test_bootstrapper_is_resolvable(): void
    {
        $bootstrapper = $this->app->make(
            NavigationManifestBootstrapperInterface::class
        );

        $this->assertInstanceOf(
            NavigationManifestBootstrapper::class,
            $bootstrapper
        );
    }
}
