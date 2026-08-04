<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Navigation\DTO\NavigationManifestData;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\PermissionMatrix;
use App\Core\Generator\DTO\SecurityDefinition;
use Illuminate\Support\Str;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory responsable de construir objetos ModuleData
 * utilizados por el CN Generator.
 *
 * Centraliza toda la lógica de construcción de nombres,
 * namespaces, rutas y opciones de los módulos.
 *
 * @package App\Core\Generator\Factories
 * @since 1.0.0
 */
final class ModuleDataFactory
{
    /*
    |--------------------------------------------------------------------------
    | create(array $definition): ModuleData
    |--------------------------------------------------------------------------
    */

    /**
     * Construye un objeto ModuleData.
     *
     * @param array<string,mixed> $definition
     */
    public function create(array $definition): ModuleData
    {
        $identity = $this->identity($definition);

        $name = $this->normalizeName(
            $identity['name']
        );

        $generation = $this->generation($definition);

        $fields = $this->fields($definition);

        $options = $this->buildOptions($generation);

        $classes = $this->buildClasses($name);

        $namespaces = $this->buildNamespaces($name);

        $paths = $this->buildPaths(
            $name,
            $identity['plural']
        );

        $securityDefinition = $this->buildSecurity($definition);

        $permissionMatrix = $this->buildPermissionMatrix($definition);

        $navigation = $this->buildNavigation($definition);

        $generation = $this->buildGeneration(
            $identity['plural'],
            $this->generation($definition),
        );

        return new ModuleData(

            /*
            |--------------------------------------------------------------------------
            | Identidad
            |--------------------------------------------------------------------------
            */

            name: $name,

            options: $options,

            singular: $identity['singular'],

            plural: $identity['plural'],

            table: $identity['table'],

            description: $identity['description'],


            /*
            |--------------------------------------------------------------------------
            | Namespaces
            |--------------------------------------------------------------------------
            */

            modelNamespace: $namespaces['modelNamespace'],

            repositoryNamespace: $namespaces['repositoryNamespace'],

            serviceNamespace: $namespaces['serviceNamespace'],

            controllerNamespace: $namespaces['controllerNamespace'],

            policyNamespace: $namespaces['policyNamespace'],

            requestNamespace: $namespaces['requestNamespace'],

            factoryNamespace: $namespaces['factoryNamespace'],

            repositoryContractNamespace: $namespaces['repositoryContractNamespace'],

            serviceContractNamespace: $namespaces['serviceContractNamespace'],

            seederNamespace: $namespaces['seederNamespace'],

            testNamespace: $namespaces['testNamespace'],

            observerNamespace: $namespaces['observerNamespace'],

            permissionNamespace: $namespaces['permissionNamespace'],

            middlewareNamespace: $namespaces['middlewareNamespace'],

            /*
            |--------------------------------------------------------------------------
            | Clases
            |--------------------------------------------------------------------------
            */

            modelClass: $classes['modelClass'],

            repositoryClass: $classes['repositoryClass'],

            repositoryInterface: $classes['repositoryInterface'],

            serviceClass: $classes['serviceClass'],

            serviceInterface: $classes['serviceInterface'],

            controllerClass: $classes['controllerClass'],

            policyClass: $classes['policyClass'],

            storeRequestClass: $classes['storeRequestClass'],

            updateRequestClass: $classes['updateRequestClass'],

            factoryClass: $classes['factoryClass'],

            seederClass: $classes['seederClass'],

            featureTestClass: $classes['featureTestClass'],

            unitTestClass: $classes['unitTestClass'],

            observerClass: $classes['observerClass'],

            permissionClass: $classes['permissionClass'],

            middlewareClass: $classes['middlewareClass'],

            /*
            |--------------------------------------------------------------------------
            | Paths
            |--------------------------------------------------------------------------
            */

            modelPath: $paths['modelPath'],

            migrationPath: $paths['migrationPath'],

            repositoryPath: $paths['repositoryPath'],

            repositoryInterfacePath: $paths['repositoryInterfacePath'],

            serviceInterfacePath: $paths['serviceInterfacePath'],

            servicePath: $paths['servicePath'],

            controllerPath: $paths['controllerPath'],

            requestPath: $paths['requestPath'],

            viewPath: $paths['viewPath'],

            routePath: $paths['routePath'],

            policyPath: $paths['policyPath'],

            factoryPath: $paths['factoryPath'],

            seederPath: $paths['seederPath'],

            featureTestPath: $paths['featureTestPath'],

            unitTestPath: $paths['unitTestPath'],

            observerPath: $paths['observerPath'],

            moduleManifestPath: $paths['moduleManifestPath'],

            middlewarePath: $paths['middlewarePath'],

            permissionPath: $paths['permissionPath'],

            /*
            |--------------------------------------------------------------------------
            | Rutas
            |--------------------------------------------------------------------------
            */

            routePrefix: $generation['routePrefix'],

            routeName: $generation['routeName'],

            viewPrefix: $generation['viewPrefix'],

            /*
            |--------------------------------------------------------------------------
            | Columns
            |--------------------------------------------------------------------------
            */

            columns: $this->buildColumns(
                $fields
            ),


            /*
            |--------------------------------------------------------------------------
            | Opciones
            |--------------------------------------------------------------------------
            */

            timestamps: $options['timestamps'],

            softDeletes: $options['softDeletes'],

            uuid: $options['uuid'],

            api: $options['api'],

            tests: $options['tests'],

            permissions: $options['permissions'],

            menu: $options['menu'],

            icon: $options['icon'],

            security: $securityDefinition,

            permissionMatrix: $permissionMatrix,

            navigation: $navigation,

        );
    }

    /*
    |--------------------------------------------------------------------------
    | private function buildNavigation
    |--------------------------------------------------------------------------
    */
    /**
     * Construye el manifiesto de navegación.
     *
     * @param array<string,mixed> $definition
     */
    private function buildNavigation(
        array $definition
    ): NavigationManifestData {

        $navigation = $definition['navigation'] ?? [];

        $groups = array_map(
            static fn(array $group): NavigationGroupData =>
            new NavigationGroupData(
                id: $group['id'],
                label: $group['label'],
                icon: $group['icon'] ?? null,
                order: $group['order'] ?? 0,
            ),
            $navigation['groups'] ?? []
        );


        $items = array_map(
            static fn(array $item): NavigationItemData =>
            new NavigationItemData(
                id: $item['id'],
                label: $item['label'],
                route: $item['route'],
                permission: $item['permission'] ?? null,
                icon: $item['icon'] ?? null,
                order: $item['order'] ?? 0,
                group: $item['group'] ?? '',
            ),
            $navigation['items'] ?? []
        );


        return new NavigationManifestData(
            module: $definition['identity']['name'],
            groups: $groups,
            items: $items,
        );
    }
    /*
    |--------------------------------------------------------------------------
    | identity(array $definition): array
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene la sección identity del manifiesto.
     *
     * @param array<string,mixed> $definition
     *
     * @return array<string,mixed>
     */
    private function identity(array $definition): array
    {
        return $definition['identity'] ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | generation(array $definition): array
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene la sección generation del manifiesto.
     *
     * @param array<string,mixed> $definition
     *
     * @return array<string,mixed>
     */
    private function generation(array $definition): array
    {
        return $definition['generation'] ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | fields(array $definition): array
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene la sección fields del manifiesto.
     *
     * @param array<string,mixed> $definition
     *
     * @return array<int,array<string,mixed>>
     */
    private function fields(array $definition): array
    {
        return $definition['fields'] ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | Construcción del DTO
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | buildSecurity(array $definition, ): ?SecurityDefinition
    |--------------------------------------------------------------------------
    */

    /**
     * Construye la definición de seguridad del módulo.
     *
     * @param array<string,mixed> $definition
     */
    private function buildSecurity(array $definition,): ?SecurityDefinition
    {

        if (
            ! isset($definition['security'])
            || $definition['security'] === []
        ) {
            return null;
        }

        return SecurityDefinition::fromArray(
            $definition['security']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | buildPermissionMatrix(array $definition, ): PermissionMatrix
    |--------------------------------------------------------------------------
    */

    /**
     * Construye la matriz de permisos del módulo.
     *
     * @param array<string,mixed> $definition
     */
    private function buildPermissionMatrix(array $definition,): PermissionMatrix
    {

        if (
            ! isset($definition['permissions'])
            || $definition['permissions'] === []
        ) {
            return new PermissionMatrix([]);
        }

        return PermissionMatrix::fromArray(
            $definition['permissions']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | buildColumns(array $fields): array
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<int,array<string,mixed>> $fields
     *
     * @return array<int,ColumnDefinition>
     */
    private function buildColumns(array $fields): array
    {
        return array_map(
            static fn(array $field): ColumnDefinition =>
            ColumnDefinition::fromArray($field),
            $fields
        );
    }

    /*
    |--------------------------------------------------------------------------
    | private function buildGeneration(string $plural,array $generation,): array
    |--------------------------------------------------------------------------
    */
    private function buildGeneration(string $plural, array $generation,): array
    {
        return [

            'routePrefix' => $generation['routePrefix'] ?? Str::kebab($plural),

            'routeName' => $generation['routeName'] ?? Str::kebab($plural),

            'viewPrefix' => $generation['viewPrefix'] ?? Str::snake($plural),

        ];
    }
    /*
    |--------------------------------------------------------------------------
    | normalizeName(string $name): string
    |--------------------------------------------------------------------------
    */

    /**
     * Normaliza el nombre del módulo.
     */
    private function normalizeName(string $name): string
    {
        return Str::studly($name);
    }

    /*
    |--------------------------------------------------------------------------
    | buildClasses(string $name): array
    |--------------------------------------------------------------------------
    */

    /**
     * Construye los nombres de las clases.
     *
     * @return array<string,string>
     */
    private function buildClasses(string $name): array
    {
        return [

            'modelClass' => $name,

            'repositoryClass' => "{$name}Repository",

            'repositoryInterface'
            => "{$name}RepositoryInterface",

            'serviceClass'
            => "{$name}Service",

            'serviceInterface'
            => "{$name}ServiceInterface",

            'controllerClass'
            => "{$name}Controller",

            'storeRequestClass'
            => "Store{$name}Request",

            'updateRequestClass'
            => "Update{$name}Request",

            'policyClass'
            => "{$name}Policy",

            'permissionClass'
            => "{$name}Permission",

            'factoryClass'
            => "{$name}Factory",

            'seederClass'
            => "{$name}Seeder",

            'featureTestClass'
            => "{$name}FeatureTest",

            'unitTestClass'
            => "{$name}UnitTest",

            'observerClass'
            => "{$name}Observer",

            'middlewareClass'
            => "{$name}Middleware",
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | buildNamespaces(string $name): array
    |--------------------------------------------------------------------------
    */

    /**
     * Construye los namespaces.
     *
     * @return array<string,string>
     */
    private function buildNamespaces(string $name): array
    {
        return [

            'modelNamespace' => 'App\\Models',

            'repositoryNamespace' => 'App\\Core\\Repositories',

            'repositoryContractNamespace' => 'App\\Core\\Contracts',

            'serviceNamespace' => 'App\\Core\\Services',

            'serviceContractNamespace' => 'App\\Core\\Contracts',

            'controllerNamespace' => 'App\\Http\\Controllers',

            'requestNamespace' => "App\\Http\\Requests\\{$name}",

            'policyNamespace' => 'App\\Policies',

            'observerNamespace' => 'App\\Observers',

            'permissionNamespace' => 'App\\Core\\Permissions',

            'middlewareNamespace' => 'App\\Http\\Middleware',

            'bindingNamespace' => 'App\\Providers',

            'factoryNamespace' => 'Database\\Factories',

            'seederNamespace' => 'Database\\Seeders',

            'testNamespace' => 'Tests\\Feature',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | buildPaths(string $name, string $plural): array
    |--------------------------------------------------------------------------
    */

    /**
     * Construye las rutas físicas.
     *
     * @return array<string,string>
     */
    private function buildPaths(string $name, string $plural): array
    {

        return [

            'modelPath'
            => app_path("Models/{$name}.php"),

            'migrationPath'
            => database_path('migrations'),

            'repositoryPath'
            => app_path("Core/Repositories/{$name}Repository.php"),

            'repositoryInterfacePath'
            => app_path("Core/Contracts/{$name}RepositoryInterface.php"),

            'serviceInterfacePath'
            => app_path("Core/Contracts/{$name}ServiceInterface.php"),

            'servicePath'
            => app_path("Core/Services/{$name}Service.php"),

            'controllerPath'
            => app_path("Http/Controllers/{$name}Controller.php"),

            'requestPath'
            => app_path("Http/Requests/{$name}"),

            'viewPath'
            => resource_path("views/{$plural}"),

            'routePath'
            => base_path(
                "routes/modules/{$plural}.php"
            ),

            'policyPath'
            => app_path("Policies/{$name}Policy.php"),

            'factoryPath'
            => database_path("factories/{$name}Factory.php"),

            'seederPath'
            => database_path("seeders/{$name}Seeder.php"),

            'featureTestPath'
            => base_path("tests/Feature/{$name}FeatureTest.php"),

            'unitTestPath'
            => base_path("tests/Unit/{$name}UnitTest.php"),

            'observerPath'
            => app_path("Observers/{$name}Observer.php"),

            'moduleManifestPath'
            => base_path("modules/{$name}.json"), // o la ruta que hayas definido para el manifiesto

            // NUEVO
            'middlewarePath' => app_path(
                "Http/Middleware/{$name}Middleware.php"
            ),

            'permissionPath'
            => app_path("Core/Permissions/{$name}Permission.php")
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | buildOptions(array $generation): array
    |--------------------------------------------------------------------------
    */

    /**
     * Construye las opciones de generación del módulo.
     *
     * @param array<string,mixed> $generation
     *
     * @return array<string,mixed>
     */
    private function buildOptions(array $generation): array
    {
        return [

            'timestamps' => $generation['timestamps'] ?? true,

            'softDeletes' => $generation['softDeletes'] ?? true,

            'uuid' => $generation['uuid'] ?? true,

            'api' => $generation['api'] ?? true,

            'tests' => $generation['tests'] ?? true,

            'permissions' => $generation['permissions'] ?? true,

            'menu' => $generation['menu'] ?? true,

            'icon' => $generation['icon'] ?? null,
        ];
    }
}
