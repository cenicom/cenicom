<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Discovery\ModuleManifestFinder;
use App\Core\Module\Factory\ModuleDefinitionFactory;
use Tests\TestCase;


final class ModuleBootstrapTest extends TestCase
{
    public function test_bootstrap_registers_modules(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/Modules')
            ),
        );

        $finder = app(ModuleManifestFinderInterface::class);

        $registrar = app(ModuleProviderRegistrarInterface::class);

        $registry = app(ModuleRegistryInterface::class);


        $bootstrap = new ModuleBootstrap(
            $registrar,
            $finder,
            $registry,
            new ModuleDefinitionFactory()
        );


        $bootstrap->bootstrap();


        $modules = $registry->all();


        $this->assertCount(3, $modules);


        $names = array_map(
            fn($module) => $module->name,
            $modules
        );

        $this->assertContains('Blog', $names);
        $this->assertContains('Users', $names);
        $this->assertContains('EmptyModule', $names);
    }

    public function test_bootstrap_is_idempotent(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/Modules')
            ),
        );

        $finder = app(ModuleManifestFinderInterface::class);

        $registrar = app(ModuleProviderRegistrarInterface::class);

        $registry = app(ModuleRegistryInterface::class);

        $bootstrap = new ModuleBootstrap(
            $registrar,
            $finder,
            $registry,
            new ModuleDefinitionFactory()
        );

        // Primera ejecución
        $bootstrap->bootstrap();

        $firstModules = $registry->all();

        $firstCount = count($firstModules);

        // Segunda ejecución
        $bootstrap->bootstrap();

        $secondModules = $registry->all();

        $secondCount = count($secondModules);

        $this->assertSame($firstCount, $secondCount);

        $this->assertCount(3, $secondModules);

        $names = array_map(
            fn($module) => $module->name,
            $secondModules
        );

        sort($names);

        $this->assertSame(
            ['Blog', 'EmptyModule', 'Users'],
            $names
        );
    }

    public function test_bootstrap_handles_empty_modules_directory(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/EmptyModules')
            ),
        );

        $finder = app(ModuleManifestFinderInterface::class);

        $registrar = app(ModuleProviderRegistrarInterface::class);

        $registry = app(ModuleRegistryInterface::class);

        $bootstrap = new ModuleBootstrap(
            $registrar,
            $finder,
            $registry,
            new ModuleDefinitionFactory()
        );

        $bootstrap->bootstrap();

        $this->assertCount(0, $registry->all());

        $this->assertSame(0, $registry->count());
    }

    public function test_bootstrap_skips_disabled_modules(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            fn() => new ModuleManifestFinder(
                base_path('tests/Fixtures/Modules')
            ),
        );

        $finder = app(ModuleManifestFinderInterface::class);

        $registrar = app(ModuleProviderRegistrarInterface::class);

        $registry = app(ModuleRegistryInterface::class);

        $bootstrap = new ModuleBootstrap(
            $registrar,
            $finder,
            $registry,
            new ModuleDefinitionFactory(),
        );

        $bootstrap->bootstrap();

        $this->assertFalse(
            $registry->has('DisabledModule')
        );

        $names = array_map(
            fn($module) => $module->name,
            $registry->all()
        );

        $this->assertNotContains(
            'DisabledModule',
            $names
        );

        $this->assertCount(
            3,
            $registry->all()
        );
    }
}
