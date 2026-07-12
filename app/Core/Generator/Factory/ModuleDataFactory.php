<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\DTO\ModuleData;

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
        $name = $this->normalizeName($definition['name']);

        $classes = $this->buildClasses($name);

        $namespaces = $this->buildNamespaces($name);

        $paths = $this->buildPaths(
            $name,
            $definition['plural']
        );

        $options = $this->buildOptions($definition);

        return new ModuleData(

            /*
            |--------------------------------------------------------------------------
            | Identidad
            |--------------------------------------------------------------------------
            */

            name: $name,

            singular: $definition['singular'],

            plural: $definition['plural'],

            table: $definition['table'],

            description: $definition['description'],

            /*
            |--------------------------------------------------------------------------
            | Namespaces
            |--------------------------------------------------------------------------
            */

            modelNamespace: $namespaces['modelNamespace'],

            repositoryNamespace: $namespaces['repositoryNamespace'],

            serviceNamespace: $namespaces['serviceNamespace'],

            controllerNamespace: $namespaces['controllerNamespace'],

            requestNamespace: $namespaces['requestNamespace'],

            contractNamespace: $namespaces['contractNamespace'],

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

            storeRequestClass: $classes['storeRequestClass'],

            updateRequestClass: $classes['updateRequestClass'],

            /*
            |--------------------------------------------------------------------------
            | Paths
            |--------------------------------------------------------------------------
            */

            modelPath: $paths['modelPath'],

            migrationPath: $paths['migrationPath'],

            repositoryPath: $paths['repositoryPath'],

            servicePath: $paths['servicePath'],

            controllerPath: $paths['controllerPath'],

            requestPath: $paths['requestPath'],

            viewPath: $paths['viewPath'],

            routePath: $paths['routePath'],

            /*
            |--------------------------------------------------------------------------
            | Rutas
            |--------------------------------------------------------------------------
            */

            routePrefix: $definition['routePrefix'],

            routeName: $definition['routeName'],

            viewPrefix: $definition['viewPrefix'],

            /*
            |--------------------------------------------------------------------------
            | Campos
            |--------------------------------------------------------------------------
            */

            fields: $definition['fields'],

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
     * Normaliza el nombre del módulo.
     */
    private function normalizeName(string $name): string
    {
        return ucfirst(
            str_replace('_', '', ucwords($name, '_'))
        );
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

            'modelNamespace'
                => 'App\\Models',

            'repositoryNamespace'
                => 'App\\Core\\Repositories',

            'serviceNamespace'
                => 'App\\Core\\Services',

            'controllerNamespace'
                => 'App\\Http\\Controllers',

            'requestNamespace'
                => "App\\Http\\Requests\\{$name}",

            'contractNamespace'
                => 'App\\Core\\Contracts',
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

            'servicePath'
                => app_path("Core/Services/{$name}Service.php"),

            'controllerPath'
                => app_path("Http/Controllers/{$name}Controller.php"),

            'requestPath'
                => app_path("Http/Requests/{$name}"),

            'viewPath'
                => resource_path("views/{$plural}"),

            'routePath'
                => base_path('routes'),
        ];
    }

    /**
     * Construye las opciones del módulo.
     *
     * @param array<string,mixed> $definition
     *
     * @return array<string,mixed>
     */
    private function buildOptions(array $definition): array
    {
        return [

            'timestamps'
                => $definition['timestamps'] ?? true,

            'softDeletes'
                => $definition['softDeletes'] ?? false,

            'uuid'
                => $definition['uuid'] ?? true,

            'api'
                => $definition['api'] ?? false,

            'tests'
                => $definition['tests'] ?? true,

            'permissions'
                => $definition['permissions'] ?? false,

            'menu'
                => $definition['menu'] ?? true,

            'icon'
                => $definition['icon'] ?? null,
        ];
    }
}
