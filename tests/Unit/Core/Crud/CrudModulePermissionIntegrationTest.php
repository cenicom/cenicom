<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

//use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
//use App\Core\Crud\Contracts\CrudPermissionServiceInterface;
use App\Core\Crud\CrudPermissionResolver;
use App\Core\Crud\CrudPermissionService;
use App\Core\Crud\CrudRegistrar;
use App\Core\Crud\CrudService;
use App\Core\Crud\Loader\CrudDefinitionLoader;
use App\Core\Crud\Registry\CrudDefinitionRegistry;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use App\Core\Crud\CrudPermissionRegistrar;
use App\Core\Security\Permissions\PermissionRegistrar;
use App\Core\Security\Permissions\PermissionRegistry;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Crud\TestPermissionCrudDefinition;

final class CrudModulePermissionIntegrationTest extends TestCase
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

    public function test_module_crud_operations_resolve_to_permissions(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new CrudDefinitionRegistry();

        $loader = new CrudDefinitionLoader(
            $definitions,
            $modules,
        );

        $registrar = new CrudRegistrar();

        $service = new CrudService(
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
                TestPermissionCrudDefinition::class,
            ],
            viewDefinitions: [],
        );

        $modules->register($module);

        $loader->load();

        Container::getInstance()->bind(
            TestPermissionCrudDefinition::class,
            fn() => new TestPermissionCrudDefinition()
        );

        $service->boot();

        $permissionService = new CrudPermissionService(
            $registrar,
            new CrudPermissionResolver(),
        );

        $permissionRegistry = new PermissionRegistry();

        $permissionRegistrar = new PermissionRegistrar(
            $permissionRegistry
        );

        $crudPermissionRegistrar = new CrudPermissionRegistrar(
            $permissionService,
            $permissionRegistrar,
        );

        $registered = $crudPermissionRegistrar->register('tests');

        self::assertSame(
            [
                'tests.view',
                'tests.create',
                'tests.update',
                'tests.delete',
            ],
            $registered
        );

        self::assertNotNull(
            $permissionRegistry->permission('tests.view')
        );

        self::assertNotNull(
            $permissionRegistry->permission('tests.create')
        );

        self::assertNotNull(
            $permissionRegistry->permission('tests.update')
        );

        self::assertNotNull(
            $permissionRegistry->permission('tests.delete')
        );
    }
}
