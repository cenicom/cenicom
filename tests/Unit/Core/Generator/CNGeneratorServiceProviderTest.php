<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\GeneratorRegistry;
use App\Core\Generator\Generators\ActionGenerator;
use App\Core\Generator\Generators\BindingGenerator;
use App\Core\Generator\Generators\ControllerGenerator;
use App\Core\Generator\Generators\FactoryGenerator;
use App\Core\Generator\Generators\FeatureTestGenerator;
use App\Core\Generator\Generators\MiddlewareGenerator;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Generators\ModuleManifestGenerator;
use App\Core\Generator\Generators\ObserverGenerator;
use App\Core\Generator\Generators\PermissionGenerator;
use App\Core\Generator\Generators\PolicyGenerator;
use App\Core\Generator\Generators\RepositoryGenerator;
use App\Core\Generator\Generators\RepositoryInterfaceGenerator;
use App\Core\Generator\Generators\RequestGenerator;
use App\Core\Generator\Generators\RouteGenerator;
use App\Core\Generator\Generators\SeederGenerator;
use App\Core\Generator\Generators\ServiceGenerator;
use App\Core\Generator\Generators\ServiceInterfaceGenerator;
use App\Core\Generator\Generators\UnitTestGenerator;
use App\Core\Generator\Generators\ViewGenerator;

use App\Providers\CNGeneratorServiceProvider;
use Tests\TestCase;

final class CNGeneratorServiceProviderTest extends TestCase
{
    public function test_registers_generator_registry(): void
    {
        $this->app->register(
            CNGeneratorServiceProvider::class
        );

        $registry = $this->app->make(
            GeneratorRegistry::class
        );

        $this->assertInstanceOf(
            GeneratorRegistry::class,
            $registry
        );
    }

    public function test_registers_module_generator(): void
    {
        $this->app->register(
            CNGeneratorServiceProvider::class
        );

        $generator = $this->app->make(
            ModuleGenerator::class
        );

        $this->assertInstanceOf(
            ModuleGenerator::class,
            $generator
        );
    }

    public function test_generator_registry_contains_registered_generators(): void
    {
        $this->app->register(
            CNGeneratorServiceProvider::class
        );

        $registry = $this->app->make(
            GeneratorRegistry::class
        );

        $generators = iterator_to_array(
            $registry->all()
        );

        $this->assertNotEmpty($generators);
    }

    public function test_generator_registry_contains_only_generator_contracts(): void
    {
        $this->app->register(
            CNGeneratorServiceProvider::class
        );

        $registry = $this->app->make(
            GeneratorRegistry::class
        );

        $generators = iterator_to_array(
            $registry->all()
        );

        $this->assertNotEmpty($generators);

        foreach ($generators as $generator) {
            $this->assertInstanceOf(
                GeneratorInterface::class,
                $generator
            );
        }
    }

    public function test_generator_registry_contains_expected_generators(): void
    {
        $this->app->register(
            CNGeneratorServiceProvider::class
        );

        $registry = $this->app->make(
            GeneratorRegistry::class
        );

        $generators = iterator_to_array(
            $registry->all()
        );

        $classes = array_map(
            static fn($generator): string => $generator::class,
            $generators
        );

        $expected = [
            ModuleManifestGenerator::class,

            ModelGenerator::class,
            MigrationGenerator::class,

            RepositoryInterfaceGenerator::class,
            RepositoryGenerator::class,

            ServiceInterfaceGenerator::class,
            ServiceGenerator::class,

            RequestGenerator::class,
            ControllerGenerator::class,
            ActionGenerator::class,

            ViewGenerator::class,
            RouteGenerator::class,

            FactoryGenerator::class,
            SeederGenerator::class,

            FeatureTestGenerator::class,
            UnitTestGenerator::class,

            PolicyGenerator::class,
            ObserverGenerator::class,
            BindingGenerator::class,
            PermissionGenerator::class,
            MiddlewareGenerator::class,
        ];

        $this->assertSame(
            $expected,
            $classes
        );
    }
}
