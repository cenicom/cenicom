<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Objeto de transferencia de datos que representa un módulo
 * del CN Generator.
 *
 * Centraliza la información necesaria para la generación
 * automática de componentes del sistema.
 *
 * @package App\Core\Generator\DTO
 * @since 1.0.0
 */
readonly class ModuleData
{
    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Identidad del módulo
    |--------------------------------------------------------------------------
    */

    private string $name;

    private string $singular;

    private string $plural;

    private string $table;

    private string $description;


    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Namespaces
    |--------------------------------------------------------------------------
    */

    private string $modelNamespace;

    private string $repositoryNamespace;

    private string $serviceNamespace;

    private string $controllerNamespace;

    private string $requestNamespace;

    private string $contractNamespace;


    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Clases generadas
    |--------------------------------------------------------------------------
    */

    private string $modelClass;

    private string $repositoryClass;

    private string $repositoryInterface;

    private string $serviceClass;

    private string $serviceInterface;

    private string $controllerClass;

    private string $storeRequestClass;

    private string $updateRequestClass;


    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Paths
    |--------------------------------------------------------------------------
    */

    private string $modelPath;

    private string $migrationPath;

    private string $repositoryPath;

    private string $servicePath;

    private string $controllerPath;

    private string $requestPath;

    private string $viewPath;

    private string $routePath;


    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Rutas y vistas
    |--------------------------------------------------------------------------
    */

    private string $routePrefix;

    private string $routeName;

    private string $viewPrefix;


    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Campos
    |--------------------------------------------------------------------------
    */
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $fields;


    /*
    |--------------------------------------------------------------------------
    | 7️⃣ Opciones
    |--------------------------------------------------------------------------
    */

    private bool $timestamps;

    private bool $softDeletes;

    private bool $uuid;

    private bool $api;

    private bool $tests;

    private bool $permissions;

    private bool $menu;

    private ?string $icon;


    /**
     * Constructor del objeto ModuleData.
     */
    public function __construct(
        string $name,
        string $singular,
        string $plural,
        string $table,
        string $description,

        string $modelNamespace,
        string $repositoryNamespace,
        string $serviceNamespace,
        string $controllerNamespace,
        string $requestNamespace,
        string $contractNamespace,

        string $modelClass,
        string $repositoryClass,
        string $repositoryInterface,
        string $serviceClass,
        string $serviceInterface,
        string $controllerClass,
        string $storeRequestClass,
        string $updateRequestClass,

        string $modelPath,
        string $migrationPath,
        string $repositoryPath,
        string $servicePath,
        string $controllerPath,
        string $requestPath,
        string $viewPath,
        string $routePath,

        string $routePrefix,
        string $routeName,
        string $viewPrefix,

        array $fields,

        bool $timestamps,
        bool $softDeletes,
        bool $uuid,
        bool $api,
        bool $tests,
        bool $permissions,
        bool $menu,
        ?string $icon,
    ) {
        $this->name = $name;
        $this->singular = $singular;
        $this->plural = $plural;
        $this->table = $table;
        $this->description = $description;

        $this->modelNamespace = $modelNamespace;
        $this->repositoryNamespace = $repositoryNamespace;
        $this->serviceNamespace = $serviceNamespace;
        $this->controllerNamespace = $controllerNamespace;
        $this->requestNamespace = $requestNamespace;
        $this->contractNamespace = $contractNamespace;

        $this->modelClass = $modelClass;
        $this->repositoryClass = $repositoryClass;
        $this->repositoryInterface = $repositoryInterface;
        $this->serviceClass = $serviceClass;
        $this->serviceInterface = $serviceInterface;
        $this->controllerClass = $controllerClass;
        $this->storeRequestClass = $storeRequestClass;
        $this->updateRequestClass = $updateRequestClass;

        $this->modelPath = $modelPath;
        $this->migrationPath = $migrationPath;
        $this->repositoryPath = $repositoryPath;
        $this->servicePath = $servicePath;
        $this->controllerPath = $controllerPath;
        $this->requestPath = $requestPath;
        $this->viewPath = $viewPath;
        $this->routePath = $routePath;

        $this->routePrefix = $routePrefix;
        $this->routeName = $routeName;
        $this->viewPrefix = $viewPrefix;

        $this->fields = $fields;

        $this->timestamps = $timestamps;
        $this->softDeletes = $softDeletes;
        $this->uuid = $uuid;
        $this->api = $api;
        $this->tests = $tests;
        $this->permissions = $permissions;
        $this->menu = $menu;
        $this->icon = $icon;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters - Identidad
    |--------------------------------------------------------------------------
    */

    public function name(): string
    {
        return $this->name();
    }

    public function singular(): string
    {
        return $this->singular;
    }

    public function plural(): string
    {
        return $this->plural;
    }

    public function table(): string
    {
        return $this->table;
    }

    public function description(): string
    {
        return $this->description;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters - Namespaces
    |--------------------------------------------------------------------------
    */

    public function modelNamespace(): string
    {
        return $this->modelNamespace;
    }

    public function repositoryNamespace(): string
    {
        return $this->repositoryNamespace;
    }

    public function serviceNamespace(): string
    {
        return $this->serviceNamespace;
    }

    public function controllerNamespace(): string
    {
        return $this->controllerNamespace;
    }

    public function requestNamespace(): string
    {
        return $this->requestNamespace;
    }

    public function contractNamespace(): string
    {
        return $this->contractNamespace;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters - Clases
    |--------------------------------------------------------------------------
    */

    public function modelClass(): string
    {
        return $this->modelClass;
    }

    public function repositoryClass(): string
    {
        return $this->repositoryClass;
    }

    public function repositoryInterface(): string
    {
        return $this->repositoryInterface;
    }

    public function serviceClass(): string
    {
        return $this->serviceClass;
    }

    public function serviceInterface(): string
    {
        return $this->serviceInterface;
    }

    public function controllerClass(): string
    {
        return $this->controllerClass;
    }

    public function storeRequestClass(): string
    {
        return $this->storeRequestClass;
    }

    public function updateRequestClass(): string
    {
        return $this->updateRequestClass;
    }

    /*
|--------------------------------------------------------------------------
| Getters - Paths
|--------------------------------------------------------------------------
*/

    public function modelPath(): string
    {
        return $this->modelPath;
    }

    public function migrationPath(): string
    {
        return $this->migrationPath;
    }

    public function repositoryPath(): string
    {
        return $this->repositoryPath;
    }

    public function servicePath(): string
    {
        return $this->servicePath;
    }

    public function controllerPath(): string
    {
        return $this->controllerPath;
    }

    public function requestPath(): string
    {
        return $this->requestPath;
    }

    public function viewPath(): string
    {
        return $this->viewPath;
    }

    public function routePath(): string
    {
        return $this->routePath;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters - Rutas y Vistas
    |--------------------------------------------------------------------------
    */

    public function routePrefix(): string
    {
        return $this->routePrefix;
    }

    public function routeName(): string
    {
        return $this->routeName;
    }

    public function viewPrefix(): string
    {
        return $this->viewPrefix;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters - Campos
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fields(): array
    {
        return $this->fields();
    }

    /*
    |--------------------------------------------------------------------------
    | Getters - Opciones
    |--------------------------------------------------------------------------
    */

    public function timestamps(): bool
    {
        return $this->timestamps;
    }

    public function softDeletes(): bool
    {
        return $this->softDeletes;
    }

    public function uuid(): bool
    {
        return $this->uuid;
    }

    public function api(): bool
    {
        return $this->api;
    }

    public function tests(): bool
    {
        return $this->tests;
    }

    public function permissions(): bool
    {
        return $this->permissions;
    }

    public function menu(): bool
    {
        return $this->menu;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }
    /*
    |--------------------------------------------------------------------------
    | Métodos qualified*
    |--------------------------------------------------------------------------
    */

    public function qualifiedModel(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedRepository(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedRepositoryInterface(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedService(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedServiceInterface(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedController(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedStoreRequest(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }

    public function qualifiedUpdateRequest(): string
    {
        return "{$this->modelNamespace()}\\{$this->modelClass()}";
    }


    /*
    |--------------------------------------------------------------------------
    | Métodos filename*
    |--------------------------------------------------------------------------
    */

    public function modelFilename(): string
    {
        return "{$this->modelClass()}.php";
    }

    public function repositoryFilename(): string
    {
        return "{$this->repositoryClass}.php";
    }

    public function serviceFilename(): string
    {
        return "{$this->serviceClass}.php";
    }

    public function controllerFilename(): string
    {
        return "{$this->controllerClass}.php";
    }

    public function migrationFilename(): string
    {
        return "create_{$this->table()}_table.php";
    }


    /*
    |--------------------------------------------------------------------------
    | Métodos views/routes
    |--------------------------------------------------------------------------
    */

    public function viewDirectory(): string
    {
        return "{$this->viewDirectory()}.index";
    }

    public function indexView(): string
    {
        return "{$this->viewPrefix}.index";
    }

    public function createView(): string
    {
        return "{$this->viewDirectory()}.index";
    }

    public function editView(): string
    {
        return "{$this->viewDirectory()}.index";
    }

    public function showView(): string
    {
        return "{$this->viewDirectory()}.index";
    }

    public function routeResource(): string
    {
        return $this->routePrefix;
    }

    public function routeIndex(): string
    {
        return "{$this->routeName}.index";
    }

    public function routeCreate(): string
    {
        return "{$this->routeCreate()}.index";
    }

    public function routeEdit(): string
    {
        return "{$this->routeName}.edit";
    }

    public function routeStore(): string
    {
        return "{$this->routeName}.store";
    }

    public function routeUpdate(): string
    {
        return "{$this->routeName}.update";
    }

    public function routeDestroy(): string
    {
        return "{$this->routeName}.destroy";
    }

    public function hasTimestamps(): bool
    {
        return $this->timestamps();
    }

    public function hasSoftDeletes(): bool
    {
        return $this->softDeletes();
    }

    public function hasUuid(): bool
    {
        return $this->uuid();
    }
}
