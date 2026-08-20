<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
//use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;
use App\Core\Crud\CrudService;
use App\Core\Crud\Loader\CrudDefinitionLoader;
use App\Core\Module\DTO\ModuleDefinition;
use Tests\Fixtures\Crud\TestCrudController;
use Tests\Fixtures\Crud\TestCrudDefinition;
use Tests\TestCase;

final class CrudModuleContainerIntegrationTest extends TestCase
{
    public function test_module_crud_flows_through_container_to_resource_service(): void
    {
        $modules = $this->app->make(
            ModuleRegistryInterface::class
        );

        $module = new ModuleDefinition(
            name: 'Test',
            namespace: 'Tests\\Fixtures\\Modules\\Test',
            basePath: __DIR__,
            manifestPath: __FILE__,
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            enabled: true,
            crudDefinitions: [
                TestCrudDefinition::class,
            ],
            viewDefinitions: [],
        );

        $modules->register($module);

        $loader = $this->app->make(
            CrudDefinitionLoader::class
        );

        $loader->load();

        $definitions = $this->app->make(
            CrudDefinitionRegistryInterface::class
        );

        self::assertSame(
            [
                TestCrudDefinition::class,
            ],
            $definitions->definitions()
        );

        $this->app->bind(
            TestCrudDefinition::class,
            fn() => new TestCrudDefinition()
        );

        $service = $this->app->make(
            CrudService::class
        );

        $service->boot();

        $resources = $this->app->make(
            ResourceServiceInterface::class
        );

        self::assertSame(
            TestCrudController::class,
            $resources->controller('tests')
        );

        self::assertSame(
            [
                'tests' => TestCrudController::class,
            ],
            $resources->all()
        );
    }
}
