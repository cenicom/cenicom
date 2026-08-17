<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Lifecycle;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Discovery\ModuleManifestFinder;
use Mockery;
use Tests\TestCase;

final class ModuleLifecycleTest extends TestCase
{
    public function test_enabled_module_completes_full_lifecycle(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/Modules')
            ),
        );

        $bootstrap = app(ModuleBootstrap::class);

        $registry = app(ModuleRegistryInterface::class);


        $bootstrap->bootstrap();


        $this->assertTrue(
            $registry->has('Blog')
        );

        $this->assertNotNull(
            $registry->get('Blog')
        );
    }

    public function test_disabled_module_is_not_registered(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/Modules')
            ),
        );

        $bootstrap = app(ModuleBootstrap::class);

        $registry = app(ModuleRegistryInterface::class);

        $bootstrap->bootstrap();

        $this->assertFalse(
            $registry->has('DisabledModule')
        );
    }

    public function test_full_lifecycle_is_idempotent(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/Modules')
            ),
        );

        $bootstrap = app(ModuleBootstrap::class);

        $registry = app(ModuleRegistryInterface::class);

        $bootstrap->bootstrap();

        $firstState = $registry->names();

        $bootstrap->bootstrap();

        $secondState = $registry->names();

        $this->assertSame(
            $firstState,
            $secondState
        );

        $this->assertCount(
            5,
            $registry->all()
        );
    }

    public function test_empty_module_directory_completes_lifecycle(): void
    {
        $finder = Mockery::mock(
            ModuleManifestFinderInterface::class
        );

        $finder
            ->shouldReceive('find')
            ->once()
            ->andReturn([]);

        $this->app->instance(
            ModuleManifestFinderInterface::class,
            $finder
        );

        $this->app->forgetInstance(
            ModuleBootstrap::class
        );

        $bootstrap = app(ModuleBootstrap::class);

        $bootstrap->bootstrap();

        $registry = app(ModuleRegistryInterface::class);

        $this->assertSame(
            [],
            $registry->all()
        );
    }
}
