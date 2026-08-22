<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Repository;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Builders\RepositoryBuilder;
use Tests\TestCase;

final class RepositoryBuilderTest extends TestCase
{
    public function test_build_generates_repository_variables(): void
    {
        $builder = new RepositoryBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey(
            'namespace',
            $variables
        );

        $this->assertArrayHasKey(
            'imports',
            $variables
        );

        $this->assertArrayHasKey(
            'repository',
            $variables
        );

        $this->assertArrayHasKey(
            'repositoryInterface',
            $variables
        );

        $this->assertArrayHasKey(
            'qualifiedRepositoryInterface',
            $variables
        );

        $this->assertArrayHasKey(
            'qualifiedModel',
            $variables
        );

        $this->assertArrayHasKey(
            'model',
            $variables
        );

        $this->assertArrayHasKey(
            'variable',
            $variables
        );

        $this->assertSame(
            'CurrencyRepository',
            $variables['repository']
        );

        $this->assertSame(
            'CurrencyRepositoryInterface',
            $variables['repositoryInterface']
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

    public function test_build_generates_repository_imports(): void
    {
        $builder = new RepositoryBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $imports = $variables['imports'];

        $this->assertStringContainsString(
            'use App\\Models\\Currency;',
            $imports
        );

        $this->assertStringContainsString(
            'use App\\Core\\Contracts\\CurrencyRepositoryInterface;',
            $imports
        );

        $this->assertStringContainsString(
            'use App\\Core\\Repositories\\BaseRepository;',
            $imports
        );
    }

    public function test_build_generates_qualified_names(): void
    {
        $builder = new RepositoryBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertSame(
            'App\\Models\\Currency',
            $variables['qualifiedModel']
        );

        $this->assertSame(
            'App\\Core\\Contracts\\CurrencyRepositoryInterface',
            $variables['qualifiedRepositoryInterface']
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
