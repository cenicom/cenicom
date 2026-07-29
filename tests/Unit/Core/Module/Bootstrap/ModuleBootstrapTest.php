<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Factory\ModuleDefinitionFactory;
use Tests\TestCase;


final class ModuleBootstrapTest extends TestCase
{
    public function test_bootstrap_registers_modules(): void
    {
        $manifest = base_path('tests/Fixtures/module.php');

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


        $this->assertCount(1, $modules);


        $this->assertSame(
            'TestModule',
            $modules[0]->name
        );
    }
}
