<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Service;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Builders\ServiceBuilder;
use Tests\TestCase;

final class ServiceBuilderTest extends TestCase
{
    public function test_build_generates_service_variables(): void
    {
        $builder = new ServiceBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey('namespace', $variables);
        $this->assertArrayHasKey('service', $variables);
        $this->assertArrayHasKey('serviceInterface', $variables);
        $this->assertArrayHasKey(
            'qualifiedServiceInterface',
            $variables
        );
        $this->assertArrayHasKey(
            'qualifiedRepositoryInterface',
            $variables
        );
        $this->assertArrayHasKey('qualifiedModel', $variables);
        $this->assertArrayHasKey('repositoryInterface', $variables);
        $this->assertArrayHasKey('model', $variables);
        $this->assertArrayHasKey('variable', $variables);
        $this->assertArrayHasKey('imports', $variables);

        $this->assertSame(
            'CurrencyService',
            $variables['service']
        );

        $this->assertSame(
            'CurrencyServiceInterface',
            $variables['serviceInterface']
        );

        $this->assertSame(
            'Currency',
            $variables['model']
        );

        $this->assertSame(
            'currency',
            $variables['variable']
        );
    }

    public function test_build_generates_service_imports(): void
    {
        $builder = new ServiceBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $imports = $variables['imports'];

        $this->assertStringContainsString(
            'use App\\Models\\Currency;',
            $imports
        );

        $this->assertStringContainsString(
            'CurrencyRepositoryInterface',
            $imports
        );

        $this->assertStringContainsString(
            'CurrencyServiceInterface',
            $imports
        );

        $this->assertStringContainsString(
            'Illuminate\\Contracts\\Pagination\\LengthAwarePaginator;',
            $imports
        );
    }

    public function test_build_generates_qualified_names(): void
    {
        $builder = new ServiceBuilder();

        $module = $this->module();

        $variables = $builder->build($module);

        $this->assertSame(
            $module->qualifiedModel(),
            $variables['qualifiedModel']
        );

        $this->assertSame(
            $module->qualifiedRepositoryInterface(),
            $variables['qualifiedRepositoryInterface']
        );

        $this->assertSame(
            $module->qualifiedServiceInterface(),
            $variables['qualifiedServiceInterface']
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
