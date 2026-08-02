<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;
use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;
use Tests\TestCase;

final class NavigationManifestCertificationTest extends TestCase
{
    public function test_navigation_manifest_components_are_resolvable(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */

        $finder = $this->app->make(
            NavigationManifestFinderInterface::class
        );

        $loader = $this->app->make(
            NavigationManifestLoaderInterface::class
        );

        $registrar = $this->app->make(
            NavigationManifestRegistrarInterface::class
        );

        $discovery = $this->app->make(
            NavigationManifestDiscoveryInterface::class
        );

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */

        $this->assertInstanceOf(
            NavigationManifestFinderInterface::class,
            $finder
        );

        $this->assertInstanceOf(
            NavigationManifestLoaderInterface::class,
            $loader
        );

        $this->assertInstanceOf(
            NavigationManifestRegistrarInterface::class,
            $registrar
        );

        $this->assertInstanceOf(
            NavigationManifestDiscoveryInterface::class,
            $discovery
        );
    }
}
