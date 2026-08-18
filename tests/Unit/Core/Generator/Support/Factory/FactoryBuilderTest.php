<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Factory;

use App\Core\Generator\Builders\FactoryBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class FactoryBuilderTest extends TestCase
{
    public function test_build_generates_factory_variables(): void
    {
        $module = $this->module();

        $variables = (new FactoryBuilder())->build($module);

        $this->assertArrayHasKey('namespace', $variables);
        $this->assertArrayHasKey('factory', $variables);
        $this->assertArrayHasKey('modelNamespace', $variables);
        $this->assertArrayHasKey('model', $variables);
        $this->assertArrayHasKey('qualifiedModel', $variables);

        $this->assertSame(
            $module->factoryNamespace(),
            $variables['namespace']
        );

        $this->assertSame(
            $module->factoryClass(),
            $variables['factory']
        );

        $this->assertSame(
            $module->modelNamespace(),
            $variables['modelNamespace']
        );

        $this->assertSame(
            $module->modelClass(),
            $variables['model']
        );

        $this->assertSame(
            $module->qualifiedModel(),
            $variables['qualifiedModel']
        );
    }

    private function module(): ModuleData
    {
        return (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],

            'generation' => [
                'routePrefix' => 'currencies',
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],
        ]);
    }
}
