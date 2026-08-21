<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;
use App\Core\Crud\CrudServiceProvider;
use App\Core\Module\DTO\ModuleDefinition;
use Tests\Fixtures\Crud\TestCrudController;
use Tests\Fixtures\Crud\TestCrudDefinition;
use Tests\TestCase;

final class CrudServiceProviderTest extends TestCase
{
    public function test_crud_definition_loader_resolves_from_container(): void
    {
        self::assertInstanceOf(
            \App\Core\Crud\Loader\CrudDefinitionLoader::class,
            $this->app->make(
                \App\Core\Crud\Loader\CrudDefinitionLoader::class
            )
        );
    }

    public function test_crud_service_resolves_from_container(): void
    {
        self::assertInstanceOf(
            \App\Core\Crud\CrudService::class,
            $this->app->make(
                \App\Core\Crud\CrudService::class
            )
        );
    }

    public function test_crud_service_provider_is_registered(): void
    {
        self::assertTrue(
            $this->app->providerIsLoaded(
                CrudServiceProvider::class
            )
        );
    }

    public function test_boot_loads_and_bootstraps_module_crud_definitions(): void
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

        $this->app->bind(
            TestCrudDefinition::class,
            fn() => new TestCrudDefinition()
        );

        $provider = $this->app->getProvider(
            CrudServiceProvider::class
        );

        $provider->boot();

        $resources = $this->app->make(
            ResourceServiceInterface::class
        );

        self::assertSame(
            TestCrudController::class,
            $resources->controller('tests')
        );
    }
}
