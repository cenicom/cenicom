<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Crud;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;
use App\Core\Crud\CrudService;
use App\Core\Crud\Loader\CrudDefinitionLoader;
use App\Core\Module\Factory\ModuleDefinitionFactory;
use App\Modules\Institution\Crud\InstitutionCrud;
use Tests\TestCase;

final class InstitutionCrudIntegrationTest extends TestCase
{
    public function test_institution_crud_is_loaded_and_registered(): void
    {
        $manifest = base_path(
            'app/Modules/Institution/module.php'
        );

        $module = $this->app
            ->make(ModuleDefinitionFactory::class)
            ->create($manifest);

        self::assertContains(
            InstitutionCrud::class,
            $module->crudDefinitions
        );

        $modules = $this->app->make(
            ModuleRegistryInterface::class
        );

        $modules->register($module);

        $loader = $this->app->make(
            CrudDefinitionLoader::class
        );

        $loader->load();

        $definitions = $this->app->make(
            CrudDefinitionRegistryInterface::class
        );

        self::assertContains(
            InstitutionCrud::class,
            $definitions->definitions()
        );

        $this->app->make(CrudService::class)->boot();

        $resources = $this->app->make(
            ResourceServiceInterface::class
        );

        self::assertSame(
            'App\\Http\\Controllers\\InstitutionController',
            $resources->controller('institutions')
        );

        self::assertSame(
            [
                'institutions' =>
                    'App\\Http\\Controllers\\InstitutionController',
            ],
            $resources->all()
        );
    }
}
