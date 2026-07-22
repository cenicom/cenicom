<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
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
    /**
     * Construye un objeto ModuleData.
     *
     * @param array<string,mixed> $definition
     */
    public function create(array $definition): ModuleData
    {
        $name = $this->normalizeName(
            $definition['identity']['name']
        );

        $classes = $this->buildClasses($name);

        $namespaces = $this->buildNamespaces($name);

        $paths = $this->buildPaths(
            $name,
            $definition['identity']['plural']
        );

        $options = $this->buildOptions(
            $definition['generation'] ?? []
        );

        $identity = $definition['identity'] ?? [];

        $generation = $definition['generation'] ?? [];

        $fields = $definition['fields'] ?? [];

        return new ModuleData(

            /*
            |--------------------------------------------------------------------------
            | Identidad
            |--------------------------------------------------------------------------
            */

            name: $name,

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


        );
    }

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

    /**
     * Normaliza el nombre del módulo.
     */
    private function normalizeName(string $name): string
    {
        return Str::studly($name);
    }

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

            'policyClass' => "{$name}Policy",

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
        ];
    }

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

            'repositoryContractNamespace' => 'App\\Core\\Repositories\\Contracts',

            'serviceNamespace' => 'App\\Core\\Services',

            'serviceContractNamespace' => 'App\\Core\\Services\\Contracts',

            'controllerNamespace' => 'App\\Http\\Controllers',

            'policyNamespace' => 'App\\Policies',

            'requestNamespace' => "App\\Http\\Requests\\{$name}",

            'factoryNamespace' => 'Database\\Factories',

            'seederNamespace' => 'Database\\Seeders',

            'testNamespace' => 'Tests\\Feature',

            'observerNamespace' => 'App\\Observers',

        ];
    }

    /**
     * Construye las rutas físicas.
     *
     * @return array<string,string>
     */
    private function buildPaths(
        string $name,
        string $plural
    ): array {

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
        ];
    }

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

            'timestamps'
            => $definition['timestamps'] ?? true,

            'softDeletes'
            => $definition['softDeletes'] ?? true,

            'uuid'
            => $definition['uuid'] ?? true,

            'api'
            => $definition['api'] ?? true,

            'tests'
            => $definition['tests'] ?? true,

            'permissions'
            => $definition['permissions'] ?? true,

            'menu'
            => $definition['menu'] ?? true,

            'icon'
            => $definition['icon'] ?? null,
        ];
    }
}
