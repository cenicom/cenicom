<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Bootstrap\CrudBootstrapper;
use App\Core\Crud\CrudRegistrar;
use App\Core\Crud\Loader\CrudDefinitionLoader;
use App\Core\Crud\Registry\CrudDefinitionRegistry;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Crud\TestCrudController;
use Tests\Fixtures\Crud\TestCrudDefinition;

final class ModuleCrudIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Container::setInstance(
            new Container()
        );
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_module_crud_definitions_are_loaded_and_registered(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new CrudDefinitionRegistry();

        $loader = new CrudDefinitionLoader(
            $definitions,
            $modules,
        );

        $registrar = new CrudRegistrar();

        $bootstrapper = new CrudBootstrapper(
            $definitions,
            $registrar,
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

        $loader->load();

        self::assertSame(
            [
                TestCrudDefinition::class,
            ],
            $definitions->definitions()
        );

        Container::getInstance()->bind(
            TestCrudDefinition::class,
            fn() => new TestCrudDefinition()
        );

        $bootstrapper->boot();

        // Resolución individual
        self::assertSame(
            TestCrudController::class,
            $registrar->controller('tests')
        );

        // Registro completo
        self::assertSame(
            [
                'tests' => TestCrudController::class,
            ],
            $registrar->all()
        );
    }
}
