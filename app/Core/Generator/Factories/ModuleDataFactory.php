<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\SecurityDefinition;
use App\Core\Generator\Support\Contracts\PathResolverInterface;
use App\Core\Generator\Support\PathResolver;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationManifestData;

use App\Core\Generator\Factories\PermissionMatrixFactory;
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
    private PathResolverInterface $paths;

    /**
     * Summary of migrationOffset
     * @var int
     */
    private int $migrationOffset = 0;


    public function __construct(
        ?PathResolverInterface $paths = null
    ) {
        $this->paths = $paths ?? new PathResolver();
    }
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

        $permissionMatrix = PermissionMatrixFactory::build(
            $name,
            $options['custom_permissions'] ?? [],
        );

        $classes = $this->buildClasses($name);

        $namespaces = $this->buildNamespaces($name);

        $paths = $this->buildPaths(
            $name,
            $identity['plural']
        );

        $securityDefinition = $this->buildSecurity($definition);

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

            actionNamespace: $namespaces['actionNamespace'],

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

            actionClass: $classes['actionClass'],

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

            actionPath: $paths['actionPath'],

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
    private function buildNavigation(array $definition): NavigationManifestData
    {

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

            'actionClass' => "{$name}Action",
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

            'modelNamespace' => "App\\Modules\\{$name}\\Models",

            'repositoryNamespace' => "App\\Modules\\{$name}\\Repositories",

            'repositoryContractNamespace' => "App\\Modules\\{$name}\\Domain\\Contracts",

            'serviceNamespace' => "App\\Modules\\{$name}\\Domain\\Services",

            'serviceContractNamespace' => "App\\Modules\\{$name}\\Domain\\Contracts",

            'controllerNamespace' => "App\\Modules\\{$name}\\Http\\Controllers",

            'requestNamespace' => "App\\Modules\\{$name}\\Http\\Requests",

            'policyNamespace' => "App\\Modules\\{$name}\\Policies",

            'observerNamespace' => "App\\Modules\\{$name}\\Observers",

            'permissionNamespace' => "App\\Modules\\{$name}\\Permissions",

            'middlewareNamespace' => "App\\Modules\\{$name}\\Http\\Middleware",

            'actionNamespace' => "App\\Modules\\{$name}\\Actions",

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
            => $this->paths->app(
                "Modules/{$name}/Models/{$name}.php"
            ),

            'migrationPath'
            => $this->paths->database(
                'migrations'
            ),

            'repositoryPath'
            => $this->paths->app(
                "Modules/{$name}/Repositories/{$name}Repository.php"
            ),

            'repositoryInterfacePath'
            => $this->paths->app(
                "Modules/{$name}/Domain/Contracts/{$name}RepositoryInterface.php"
            ),

            'serviceInterfacePath'
            => $this->paths->app(
                "Modules/{$name}/Domain/Contracts/{$name}ServiceInterface.php"
            ),

            'servicePath'
            => $this->paths->app(
                "Modules/{$name}/Domain/Services/{$name}Service.php"
            ),

            'controllerPath'
            => $this->paths->app(
                "Modules/{$name}/Http/Controllers/{$name}Controller.php"
            ),

            'requestPath'
            => $this->paths->app(
                "Modules/{$name}/Http/Requests"
            ),

            'viewPath'
            => $this->paths->resource(
                "views/{$plural}"
            ),

            'routePath'
            => $this->paths->routes(
                "modules/{$plural}.php"
            ),

            'policyPath'
            => $this->paths->app(
                "Modules/{$name}/Policies/{$name}Policy.php"
            ),

            'factoryPath'
            => $this->paths->database(
                "factories/{$name}Factory.php"
            ),

            'seederPath'
            => $this->paths->database(
                "seeders/{$name}Seeder.php"
            ),

            'featureTestPath'
            => $this->paths->app(
                "tests/Feature/{$name}FeatureTest.php"
            ),

            'unitTestPath'
            => $this->paths->app(
                "tests/Unit/{$name}UnitTest.php"
            ),

            'observerPath'
            => $this->paths->app(
                "Modules/{$name}/Observers/{$name}Observer.php"
            ),

            'moduleManifestPath'
            => $this->paths->app(
                "modules/{$name}.json"
            ), // o la ruta que hayas definido para el manifiesto

            // NUEVO
            'middlewarePath' => $this->paths->app(
                "Modules/{$name}/Http/Middleware/{$name}Middleware.php"
            ),

            'permissionPath'
            => $this->paths->app(
                "Modules/{$name}//Permissions/{$name}Permission.php"
            ),

            'actionPath' => $this->paths->app(
                "Modules/{$name}/Actions/{$name}Action.php"
            ),
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

            'custom_permissions' => $generation['custom_permissions'] ?? [],

            'menu' => $generation['menu'] ?? true,

            'icon' => $generation['icon'] ?? null,
        ];
    }

    public function fullCrud(array $definition): ModuleData
    {
        $definition['generation'] = array_merge(
            $definition['generation'] ?? [],
            $this->fullCrudOptions(),
        );

        return $this->create($definition);
    }


    private function fullCrudOptions(): array
    {
        return array_merge(
            $this->timestampsOption(),
            $this->softDeletesOption(),
            $this->testsOption(),
            $this->uuidOption(),
            $this->apiOption(),
            $this->permissionsOption(),
            $this->menuOption(),
        );
    }


    private function timestampsOption(): array
    {
        return [
            'timestamps' => true,
        ];
    }


    private function softDeletesOption(): array
    {
        return [
            'softDeletes' => true,
        ];
    }


    private function testsOption(): array
    {
        return [
            'tests' => true,
        ];
    }


    private function uuidOption(): array
    {
        return [
            'uuid' => true,
        ];
    }


    private function apiOption(): array
    {
        return [
            'api' => true,
        ];
    }


    private function permissionsOption(): array
    {
        return [
            'permissions' => true,
        ];
    }


    private function menuOption(): array
    {
        return [
            'menu' => true,
        ];
    }
}
