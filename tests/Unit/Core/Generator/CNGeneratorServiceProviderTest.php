<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\GeneratorRegistry;
use App\Core\Generator\Generators\ActionGenerator;
use App\Core\Generator\Generators\ControllerGenerator;
use App\Core\Generator\Generators\ModuleGenerator;
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

    public function test_action_generator_precedes_controller_generator(): void
    {
        $this->app->register(
            CNGeneratorServiceProvider::class
        );

        $registry = $this->app->make(
            GeneratorRegistry::class
        );

        $classes = array_map(
            static fn($generator): string => $generator::class,
            iterator_to_array($registry->all())
        );

        $actionIndex = array_search(
            ActionGenerator::class,
            $classes,
            true
        );

        $controllerIndex = array_search(
            ControllerGenerator::class,
            $classes,
            true
        );

        $this->assertIsInt($actionIndex);
        $this->assertIsInt($controllerIndex);

        $this->assertTrue(
            $actionIndex < $controllerIndex,
            'ActionGenerator must be registered before ControllerGenerator.'
        );
    }
}
