<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Support\PathResolver;
use Tests\TestCase;

/**
 * Clase base para todos los GeneratorTest del CN Generator.
 *
 * Proporciona la infraestructura común para ejecutar pruebas de los
 * generadores en un entorno completamente aislado del proyecto.
 *
 * Responsabilidades:
 * - Crear y destruir el sandbox temporal.
 * - Proveer rutas temporales para generación de archivos.
 * - Facilitar la construcción de ModuleData para pruebas.
 * - Exponer helpers reutilizables para validaciones.
 *
 * Todas las pruebas de generadores deben heredar de esta clase.
 */
abstract class GeneratorTestCase extends TestCase
{
    /**
     * Ruta raíz del sandbox temporal.
     */
    protected string $sandboxPath;

    /**
     *  Nombre o identificador del sandbox
     */
    protected string $sandboxName;

    /**
     * Summary of sandboxInitialized
     * @var bool
     */
    protected bool $sandboxInitialized = false;

    /**
     * Summary of moduleDataFactory
     * @var ModuleDataFactory
     */
    protected ModuleDataFactory $moduleDataFactory;

    /**
     * Inicializa la infraestructura común de pruebas.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sandboxName = uniqid('generator_', true);

        $this->sandboxPath =
            sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . $this->sandboxName;

        mkdir($this->sandboxPath, 0777, true);

        mkdir($this->appPath(), 0777, true);
        mkdir($this->resourcesPath(), 0777, true);
        mkdir($this->databasePath(), 0777, true);
        mkdir($this->routesPath(), 0777, true);

        $paths = new PathResolver(
            appBase: $this->appPath(),
            resourceBase: $this->resourcesPath(),
            databaseBase: $this->databasePath(),
            routesBase: $this->routesPath(),
        );

        $this->moduleDataFactory =
            new ModuleDataFactory($paths);
    }

    /**
     * Libera los recursos utilizados por el sandbox.
     */
    protected function tearDown(): void
    {
        if ($this->sandboxInitialized) {
            $this->deleteDirectory($this->sandboxPath);
        }

        $this->deleteDirectory(
            $this->sandboxPath
        );

        parent::tearDown();
    }

    /**
     * Undocumented function
     *
     * @param string $directory
     * @return void
     */
    private function deleteDirectory(string $directory): void
    {

        if (!is_dir($directory)) {
            return;
        }

        $items = array_diff(
            scandir($directory),
            ['.', '..']
        );

        foreach ($items as $item) {

            $path = $directory
                . DIRECTORY_SEPARATOR
                . $item;

            if (is_dir($path)) {

                $this->deleteDirectory($path);
            } else {

                unlink($path);
            }
        }

        rmdir($directory);
    }

    /**
     * Construye un ModuleData base para pruebas.
     */

    protected function createModuleData(array $overrides = []): ModuleData
    {

        $definition = array_replace_recursive(
            [
                'identity' => [
                    'name' => 'Currency',
                    'singular' => 'currency',
                    'plural' => 'currencies',
                    'table' => 'currencies',
                    'description' => 'Currency module',
                ],

                'generation' => [
                    'routePrefix' => 'currencies',
                    'routeName' => 'currencies',
                    'viewPrefix' => 'currencies',
                ],

                'fields' => [
                    $this->stringField('name', [
                        'required' => true,
                    ]),

                    $this->stringField('symbol', [
                        'required' => true,
                    ]),
                ],
            ],

            $overrides
        );

        return $this->moduleDataFactory->create($definition);
    }

    /************************************************************************
     *
     * Ruthers para construcción de rutas de prueba
     *
     * ***********************************************************************/

    /**
     * Devuelve la ruta raíz del sandbox.
     */
    protected function sandboxPath(): string
    {
        return $this->sandboxPath;
    }

    /**
     * Construye una ruta absoluta dentro del sandbox.
     */
    protected function path(string ...$segments): string
    {
        if ($segments === []) {
            return $this->sandboxPath();
        }

        return $this->sandboxPath()
            . DIRECTORY_SEPARATOR
            . implode(DIRECTORY_SEPARATOR, $segments);
    }

    /**
     * Devuelve la ruta del directorio app.
     */
    protected function appPath(): string
    {
        return $this->path('app');
    }

    /**
     * Devuelve la ruta del directorio resources.
     */
    protected function resourcesPath(): string
    {
        return $this->path('resources');
    }

    /**
     * Devuelve la ruta del directorio routes.
     */
    protected function routesPath(): string
    {
        return $this->path('routes');
    }

    /**
     * Devuelve la ruta del directorio database.
     */
    protected function databasePath(): string
    {
        return $this->path('database');
    }

    /**
     * Devuelve la ruta del directorio storage.
     */
    protected function storagePath(): string
    {
        return $this->path('storage');
    }

    /************************************************************************
     *
     * Helpers para validaciones de pruebas
     *
     * ***********************************************************************/

    /**
     * Devuelve la ruta del directorio app/Models.
     */
    protected function modelsPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Models';
    }

    /**
     * Devuelve la ruta del directorio app/Repositories.
     */
    protected function repositoriesPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Repositories';
    }

    /**
     * Devuelve la ruta del directorio app/Services.
     */
    protected function servicesPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Services';
    }

    /**
     * Devuelve la ruta del directorio app/Actions.
     */
    protected function actionsPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Actions';
    }

    /**
     * Devuelve la ruta del directorio app/Contracts.
     */
    protected function contractsPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Contracts';
    }

    /**
     * Devuelve la ruta del directorio app/Http.
     */
    protected function httpPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Http';
    }

    /**
     * Devuelve la ruta del directorio app/Http/Controllers.
     */
    protected function controllersPath(): string
    {
        return $this->httpPath() . DIRECTORY_SEPARATOR . 'Controllers';
    }

    /**
     * Devuelve la ruta del directorio app/Http/Requests.
     */
    protected function requestsPath(): string
    {
        return $this->httpPath() . DIRECTORY_SEPARATOR . 'Requests';
    }

    /**
     * Devuelve la ruta del directorio app/Providers.
     */
    protected function providersPath(): string
    {
        return $this->appPath() . DIRECTORY_SEPARATOR . 'Providers';
    }

    /************************************************************************
     *
     * Resources paths
     *
     * ***********************************************************************/

    /**
     * Devuelve la ruta del directorio resources/views.
     */
    protected function viewsPath(): string
    {
        return $this->resourcesPath() . DIRECTORY_SEPARATOR . 'views';
    }

    /**
     * Devuelve la ruta del directorio resources/views/components.
     */
    protected function componentsPath(): string
    {
        return $this->viewsPath() . DIRECTORY_SEPARATOR . 'components';
    }

    /**
     * Devuelve la ruta del directorio resources/views/layouts.
     */
    protected function layoutsPath(): string
    {
        return $this->viewsPath() . DIRECTORY_SEPARATOR . 'layouts';
    }

    /**
     * Devuelve la ruta del directorio resources/views/partials.
     */
    protected function partialsPath(): string
    {
        return $this->viewsPath() . DIRECTORY_SEPARATOR . 'partials';
    }

    /**
     * Devuelve la ruta del directorio resources/lang.
     */
    protected function langPath(): string
    {
        return $this->resourcesPath() . DIRECTORY_SEPARATOR . 'lang';
    }

    /**
     * Devuelve la ruta del directorio resources/css.
     */
    protected function cssPath(): string
    {
        return $this->resourcesPath() . DIRECTORY_SEPARATOR . 'css';
    }

    /**
     * Devuelve la ruta del directorio resources/js.
     */
    protected function jsPath(): string
    {
        return $this->resourcesPath() . DIRECTORY_SEPARATOR . 'js';
    }

    /**
     * Devuelve la ruta del directorio resources/images.
     */
    protected function imagesPath(): string
    {
        return $this->resourcesPath() . DIRECTORY_SEPARATOR . 'images';
    }

    /************************************************************************
     *
     * Routes & Database Helpers
     *
     * ***********************************************************************/

    /**
     * Devuelve la ruta del archivo routes/web.php.
     */
    protected function webRoutesPath(): string
    {
        return $this->routesPath() . DIRECTORY_SEPARATOR . 'web.php';
    }

    /**
     * Devuelve la ruta del archivo routes/api.php.
     */
    protected function apiRoutesPath(): string
    {
        return $this->routesPath() . DIRECTORY_SEPARATOR . 'api.php';
    }

    /**
     * Devuelve la ruta del archivo routes/console.php.
     */
    protected function consoleRoutesPath(): string
    {
        return $this->routesPath() . DIRECTORY_SEPARATOR . 'console.php';
    }

    /**
     * Devuelve la ruta del archivo routes/channels.php.
     */
    protected function channelsRoutesPath(): string
    {
        return $this->routesPath() . DIRECTORY_SEPARATOR . 'channels.php';
    }

    /**
     * Devuelve la ruta del directorio database/migrations.
     */
    protected function migrationsPath(): string
    {
        return $this->databasePath() . DIRECTORY_SEPARATOR . 'migrations';
    }

    /**
     * Devuelve la ruta del directorio database/seeders.
     */
    protected function seedersPath(): string
    {
        return $this->databasePath() . DIRECTORY_SEPARATOR . 'seeders';
    }

    /**
     * Devuelve la ruta del directorio database/factories.
     */
    protected function factoriesPath(): string
    {
        return $this->databasePath() . DIRECTORY_SEPARATOR . 'factories';
    }

    /************************************************************************
     *
     * Generic Helpers
     *
     * ***********************************************************************/

    /**
     * Devuelve una ruta dentro del directorio app.
     */
    protected function appFile(string ...$segments): string
    {
        return $this->path('app', ...$segments);
    }

    protected function resourceFile(string ...$segments): string
    {
        return $this->path('resources', ...$segments);
    }

    protected function routeFile(string ...$segments): string
    {
        return $this->path('routes', ...$segments);
    }

    protected function databaseFile(string ...$segments): string
    {
        return $this->path('database', ...$segments);
    }

    protected function storageFile(string ...$segments): string
    {
        return $this->path('storage', ...$segments);
    }
    /**
     * Determina si un archivo o directorio existe.
     */
    protected function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Convierte una ruta absoluta del sandbox en una ruta relativa.
     */
    protected function relativePath(string $path): string
    {
        $relative = str_replace(
            $this->sandboxPath() . DIRECTORY_SEPARATOR,
            '',
            $path
        );

        return str_replace('\\', '/', $relative);
    }

    /**
     * Construye la definición base de un campo.
     *
     * @param string $name Nombre del campo.
     * @param string $type Tipo del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function field(
        string $name,
        string $type,
        array $attributes = []
    ): array {
        $field = [
            'name' => $name,
            'type' => $type,
        ];

        return array_replace($field, $attributes);
    }

    /**
     * Construye un campo de tipo string.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function stringField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'string', $attributes);
    }

    /**
     * Construye un campo de tipo text.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function textField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'text', $attributes);
    }

    /**
     * Construye un campo de tipo integer.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function integerField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'integer', $attributes);
    }

    /**
     * Construye un campo de tipo boolean.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function booleanField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'boolean', $attributes);
    }

    /**
     * Construye un campo de tipo decimal.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function decimalField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'decimal', $attributes);
    }

    /**
     * Construye un campo de tipo date.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function dateField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'date', $attributes);
    }

    /**
     * Construye un campo de tipo datetime.
     *
     * @param string $name Nombre del campo.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function datetimeField(
        string $name,
        array $attributes = []
    ): array {
        return $this->field($name, 'datetime', $attributes);
    }

    /**
     * Construye un campo identificador primario.
     *
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function idField(
        array $attributes = []
    ): array {
        return $this->field('id', 'id', $attributes);
    }

    /**
     * Construye un campo UUID.
     *
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function uuidField(
        array $attributes = []
    ): array {
        return $this->field('uuid', 'uuid', $attributes);
    }

    /**
     * Construye un campo de clave foránea.
     *
     * @param string $name Nombre del campo.
     * @param string $references Modelo o tabla de referencia.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function foreignIdField(
        string $name,
        string $references,
        array $attributes = []
    ): array {
        return $this->field(
            $name,
            'foreignId',
            array_replace(
                [
                    'references' => $references,
                ],
                $attributes
            )
        );
    }

    /**
     * Construye una relación belongsTo.
     *
     * @param string $model Modelo relacionado.
     * @param array<string, mixed> $attributes Atributos adicionales.
     *
     * @return array<string, mixed>
     */
    protected function belongsToField(
        string $model,
        array $attributes = []
    ): array {
        return $this->field(
            strtolower($model),
            'belongsTo',
            array_replace(
                [
                    'model' => $model,
                ],
                $attributes
            )
        );
    }

    /**
     * Construye una relación hasOne.
     *
     * @param string $model Modelo relacionado.
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    protected function hasOneField(
        string $model,
        array $attributes = []
    ): array {
        return $this->field(
            strtolower($model),
            'hasOne',
            array_replace(
                [
                    'model' => $model,
                ],
                $attributes
            )
        );
    }

    /**
     * Construye una relación hasMany.
     *
     * @param string $model Modelo relacionado.
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    protected function hasManyField(
        string $model,
        array $attributes = []
    ): array {
        return $this->field(
            strtolower($model),
            'hasMany',
            array_replace(
                [
                    'model' => $model,
                ],
                $attributes
            )
        );
    }

    /**
     * Construye una relación belongsToMany.
     *
     * @param string $model Modelo relacionado.
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    protected function belongsToManyField(
        string $model,
        array $attributes = []
    ): array {
        return $this->field(
            strtolower($model),
            'belongsToMany',
            array_replace(
                [
                    'model' => $model,
                ],
                $attributes
            )
        );
    }

    /**
     * Devuelve una colección vacía de campos.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function emptyFields(): array
    {
        return [];
    }

    /**
     * Construye una colección de campos.
     *
     * @param array<string, mixed> ...$fields
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fields(array ...$fields): array
    {
        return $fields;
    }

    /**
     * Devuelve una colección de campos CRUD estándar.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function crudFields(): array
    {
        return $this->fields(
            $this->idField(),
            $this->stringField('name'),
            $this->textField('description', [
                'nullable' => true,
            ]),
            $this->booleanField('active', [
                'default' => true,
            ])
        );
    }

    /**
     * Devuelve los campos de auditoría.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function timestampFields(): array
    {
        return $this->fields(
            $this->datetimeField('created_at'),
            $this->datetimeField('updated_at')
        );
    }

    /**
     * Combina múltiples colecciones de campos.
     *
     * @param array<int, array<string, mixed>> ...$collections
     *
     * @return array<int, array<string, mixed>>
     */
    protected function mergeFields(array ...$collections): array
    {
        return array_merge(...$collections);
    }

    /**
     * Construye una opción de generación.
     *
     * @param string $name Nombre de la opción.
     * @param mixed $value Valor de la opción.
     *
     * @return array<string, mixed>
     */
    protected function option(
        string $name,
        mixed $value = true
    ): array {
        return [
            $name => $value,
        ];
    }

    /**********************************************************************
     * Helpers para opciones de generación
     **********************************************************************/

    protected function timestampsOption(
        bool $enabled = true
    ): array {
        return $this->option('timestamps', $enabled);
    }

    protected function generatorOptions(array ...$options): array
    {
        return array_merge(...$options);
    }

    protected function mergeOptions(array ...$collections): array
    {
        return array_merge(...$collections);
    }

    protected function emptyOptions(): array
    {
        return [];
    }

    protected function defaultOptions(): array
    {
        return $this->generatorOptions(
            $this->timestampsOption(),
            $this->softDeletesOption()
        );
    }

    /**
     * Construye un módulo simple para pruebas.
     *
     * @return ModuleData
     */
    protected function simpleModule(): ModuleData
    {
        return $this->createModuleData([
            'fields' => [
                $this->idField(),
                $this->stringField('name'),
            ],
            'options' => $this->emptyOptions(),
        ]);
    }

    /**
     * Construye un módulo CRUD estándar.
     *
     * @return ModuleData
     */
    protected function crudModule(): ModuleData
    {
        return $this->createModuleData([
            'fields' => $this->crudFields(),

            'options' => $this->generatorOptions(
                $this->timestampsOption(),
                $this->softDeletesOption(),
                $this->testsOption(),
            ),
        ]);
    }

    /**
     * Construye un módulo orientado a API.
     *
     * @return ModuleData
     */
    protected function apiModule(): ModuleData
    {
        return $this->createModuleData([
            'fields' => [
                $this->uuidField(),
                $this->stringField('name'),
            ],

            'options' => $this->generatorOptions(
                $this->uuidOption(),
                $this->apiOption(),
                $this->testsOption(),
            ),
        ]);
    }

    /**
     * Construye un módulo completo.
     *
     * @return ModuleData
     */
    protected function fullModule(): ModuleData
    {
        return $this->createModuleData([
            'fields' => $this->mergeFields(
                $this->crudFields(),
                [
                    $this->uuidField(),
                    $this->belongsToField('Category'),
                ]
            ),

            'options' => $this->generatorOptions(
                $this->timestampsOption(),
                $this->softDeletesOption(),
                $this->uuidOption(),
                $this->apiOption(),
                $this->permissionsOption(),
                $this->menuOption(),
                $this->testsOption(),
            ),
        ]);
    }

    /**
     * Configuración Soft Deletes.
     */
    private function softDeletesOption(): array
    {
        return [
            'softDeletes' => true,
        ];
    }

    /**
     * Configuración generación de tests.
     */
    private function testsOption(): array
    {
        return [
            'tests' => true,
        ];
    }

    /**
     * Configuración UUID.
     */
    private function uuidOption(): array
    {
        return [
            'uuid' => true,
        ];
    }

    /**
     * Configuración API.
     */
    private function apiOption(): array
    {
        return [
            'api' => true,
        ];
    }

    /**
     * Configuración permisos.
     */
    private function permissionsOption(): array
    {
        return [
            'permissions' => true,
        ];
    }

    /**
     * Configuración menú navegación.
     */
    private function menuOption(): array
    {
        return [
            'menu' => true,
        ];
    }

    /**
     * Preset completo CRUD.
     *
     * Habilita todas las capacidades estándar
     * del generador CN.
     */
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
}
