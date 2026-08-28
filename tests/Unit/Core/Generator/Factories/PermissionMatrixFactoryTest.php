<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Factories;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\PermissionDefinition;
use App\Core\Generator\DTO\PermissionMatrix;
use App\Core\Generator\Factories\PermissionMatrixFactory;
use PHPUnit\Framework\TestCase;

final class PermissionMatrixFactoryTest extends TestCase
{
    public function test_build_returns_permission_matrix(): void
    {
        $module = $this->makeModule();

        $matrix = PermissionMatrixFactory::build($module);

        $this->assertInstanceOf(
            PermissionMatrix::class,
            $matrix
        );
    }

    public function test_build_generates_crud_permissions(): void
    {
        $module = $this->makeModule();

        $matrix = PermissionMatrixFactory::build($module);

        $permissions = $matrix->permissions();

        $this->assertCount(4, $permissions);

        $this->assertContainsOnlyInstancesOf(
            PermissionDefinition::class,
            $permissions
        );

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
            ],
            $permissionNames
        );
    }

    public function test_build_generates_custom_permissions(): void
    {
        $module = $this->makeModule([
            'custom_permissions' => [
                'approve',
                'export',
            ],
        ]);

        $matrix = PermissionMatrixFactory::build($module);

        $permissions = $matrix->permissions();

        $this->assertCount(6, $permissions);

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
                'institution.approve',
                'institution.export',
            ],
            $permissionNames
        );
    }

    public function test_build_normalizes_module_name_and_custom_permissions(): void
    {
        $module = $this->makeModule(
            [
                'custom_permissions' => [
                    ' Approve ',
                    'Export_Data',
                ],
            ],
            ' Institution '
        );

        $matrix = PermissionMatrixFactory::build($module);

        $permissions = $matrix->permissions();

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
                'institution.approve',
                'institution.export-data',
            ],
            $permissionNames
        );
    }

    public function test_build_removes_duplicate_permissions(): void
    {
        $module = $this->makeModule([
            'custom_permissions' => [
                'view',
                'create',
                'VIEW',
                ' view ',
            ],
        ]);

        $matrix = PermissionMatrixFactory::build($module);

        $permissions = $matrix->permissions();

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
            ],
            $permissionNames
        );

        $this->assertCount(4, $permissions);
    }

    /**
     * Construye un ModuleData mínimo para las pruebas.
     *
     * @param array<string,mixed> $options
     */
    private function makeModule(
        array $options = [],
        string $name = 'Institution',
    ): ModuleData {
        return new ModuleData(
            name: $name,
            singular: 'institution',
            plural: 'institutions',
            table: 'institutions',
            description: 'Institution module',

            modelNamespace: 'App\\Modules\\Institution\\Models',
            repositoryNamespace: 'App\\Modules\\Institution\\Repositories',
            serviceNamespace: 'App\\Modules\\Institution\\Services',
            controllerNamespace: 'App\\Http\\Controllers',
            policyNamespace: 'App\\Modules\\Institution\\Policies',
            requestNamespace: 'App\\Modules\\Institution\\Requests',
            factoryNamespace: 'Database\\Factories',
            repositoryContractNamespace: 'App\\Modules\\Institution\\Contracts',
            serviceContractNamespace: 'App\\Modules\\Institution\\Contracts',
            seederNamespace: 'Database\\Seeders',
            testNamespace: 'Tests\\Unit\\Modules\\Institution',
            observerNamespace: 'App\\Modules\\Institution\\Observers',
            permissionNamespace: 'App\\Modules\\Institution\\Permissions',
            middlewareNamespace: 'App\\Modules\\Institution\\Middleware',
            actionNamespace: 'App\\Modules\\Institution\\Actions',

            modelClass: 'Institution',
            repositoryClass: 'InstitutionRepository',
            repositoryInterface: 'InstitutionRepositoryInterface',
            serviceClass: 'InstitutionService',
            serviceInterface: 'InstitutionServiceInterface',
            controllerClass: 'InstitutionController',
            policyClass: 'InstitutionPolicy',
            storeRequestClass: 'StoreInstitutionRequest',
            updateRequestClass: 'UpdateInstitutionRequest',
            factoryClass: 'InstitutionFactory',
            seederClass: 'InstitutionSeeder',
            featureTestClass: 'InstitutionFeatureTest',
            unitTestClass: 'InstitutionUnitTest',
            observerClass: 'InstitutionObserver',
            permissionClass: 'InstitutionPermission',
            middlewareClass: 'InstitutionMiddleware',
            actionClass: 'InstitutionAction',

            modelPath: 'app/Modules/Institution/Models/Institution.php',
            migrationPath: 'database/migrations/create_institutions_table.php',
            repositoryPath: 'app/Modules/Institution/Repositories/InstitutionRepository.php',
            repositoryInterfacePath: 'app/Modules/Institution/Contracts/InstitutionRepositoryInterface.php',
            servicePath: 'app/Modules/Institution/Services/InstitutionService.php',
            serviceInterfacePath: 'app/Modules/Institution/Contracts/InstitutionServiceInterface.php',
            controllerPath: 'app/Http/Controllers/InstitutionController.php',
            policyPath: 'app/Modules/Institution/Policies/InstitutionPolicy.php',
            requestPath: 'app/Modules/Institution/Requests',
            factoryPath: 'database/factories/InstitutionFactory.php',
            viewPath: 'resources/views/institution',
            routePath: 'routes/institution.php',
            seederPath: 'database/seeders/InstitutionSeeder.php',
            featureTestPath: 'tests/Feature/Modules/Institution/InstitutionFeatureTest.php',
            unitTestPath: 'tests/Unit/Modules/Institution/InstitutionUnitTest.php',
            observerPath: 'app/Modules/Institution/Observers/InstitutionObserver.php',
            moduleManifestPath: 'app/Modules/Institution/module.php',
            middlewarePath: 'app/Modules/Institution/Middleware/InstitutionMiddleware.php',
            permissionPath: 'app/Modules/Institution/Permissions/InstitutionPermission.php',
            actionPath: 'app/Modules/Institution/Actions/InstitutionAction.php',

            routePrefix: 'institutions',
            routeName: 'institutions',
            viewPrefix: 'institution',

            columns: [],

            options: array_merge(
                [
                    'custom_permissions' => [],
                ],
                $options
            ),

            timestamps: true,
            softDeletes: false,
            uuid: false,
            api: false,
            tests: true,
            permissions: true,

            menu: true,
            icon: null,

            security: null,
            permissionMatrix: null,
            navigation: null,
        );
    }
}
