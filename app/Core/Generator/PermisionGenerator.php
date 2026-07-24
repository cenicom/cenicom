<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\PermissionDefinition;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Processors\PermissionProcessor;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente todos los artefactos relacionados
 * con los permisos de un módulo.
 *
 * Responsable de generar:
 *
 * - Definiciones de permisos
 * - Seeder de permisos
 * - Policies (opcional)
 * - Middleware (opcional)
 *
 * @package App\Core\Generator\Generators
 * @since 2.0.0
 */
final class PermissionGenerator extends BaseGenerator
{
    /*
    |--------------------------------------------------------------------------
    | Stubs
    |--------------------------------------------------------------------------
    */

    private const PERMISSIONS_STUB = 'permissions.stub';

    private const SEEDER_STUB = 'permissions/seeder.stub';


    /*
    |--------------------------------------------------------------------------
    | public function __construct
    |--------------------------------------------------------------------------
    */

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly PermissionProcessor $processor,
    ) {

        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GeneratorInterface
    |--------------------------------------------------------------------------
    */

    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(
        ModuleData $module
    ): bool {
        return true;
    }

    /**
     * Ejecuta la generación completa de permisos del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $result = new GeneratorResult();

        $result->merge(
            $this->generatePermissions($module)
        );

        return $result;
    }


    /**
     * Genera el archivo principal de permisos.
     */
    private function generatePermissions(
        ModuleData $module
    ): GeneratorResult {

        return $this->generateResult(
            self::PERMISSIONS_STUB,
            $module->permissionPath(),
            $this->buildVariables($module)
        );
    }


    /**
     * Construye las variables utilizadas por los stubs.
     *
     * @return array<string,mixed>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        return array_merge(
            $this->defaultVariables($module),
            [

                'moduleName' => $module->name(),

                'moduleNamespace' => $module->modelNamespace(),

                'seeder' => $module->seederClass(),

                'seederNamespace' => $module->seederNamespace(),

                'imports' => $this->buildImports(),

                'constants' => $this->buildConstants($module),

                'permissionDefinitions' => $this->buildPermissionDefinitions($module),

                'permissionArray' => $this->buildPermissionArray($module),

            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resolución
    |--------------------------------------------------------------------------
    */

    /**
     * @return PermissionDefinition[]
     */
    private function resolvePermissions(
        ModuleData $module
    ): array {
        return $this->processor->process($module);
    }

    /**
     * Construye las definiciones de permisos.
     */
    private function buildPermissionDefinitions(
        ModuleData $module
    ): string {

        $definitions = [];

        foreach ($this->resolvePermissions($module) as $permission) {

            $definitions[] = sprintf(
                <<<'PHP'
new PermissionDefinition(
    name: '%s',
    action: '%s',
    permission: '%s',
    group: '%s',
    guard: '%s',
    description: %s,
)
PHP,
                $permission->name(),
                $permission->action(),
                $permission->permission(),
                $permission->group(),
                $permission->guard(),
                $permission->description() === null
                    ? 'null'
                    : "'" . addslashes($permission->description()) . "'"
            );
        }

        return implode(
            PHP_EOL . PHP_EOL,
            $definitions
        );
    }

    /**
     * Construye el arreglo de permisos.
     */
    private function buildPermissionArray(
        ModuleData $module
    ): string {

        $items = [];

        foreach ($this->resolvePermissions($module) as $permission) {

            $items[] = sprintf(
                <<<'PHP'
[
    'name' => '%s',
    'action' => '%s',
    'permission' => '%s',
    'group' => '%s',
    'guard' => '%s',
    'description' => %s,
]
PHP,
                $permission->name(),
                $permission->action(),
                $permission->permission(),
                $permission->group(),
                $permission->guard(),
                $permission->description() === null
                    ? 'null'
                    : "'" . addslashes($permission->description()) . "'"
            );
        }

        return implode(
            ',' . PHP_EOL . PHP_EOL,
            $items
        );
    }

    /**
     * Construye los imports necesarios.
     */
    private function buildImports(): string
    {
        $imports = [

            'use App\Core\Generator\DTO\PermissionDefinition;',

        ];

        $imports = array_unique($imports);

        sort($imports);

        return implode(
            PHP_EOL,
            $imports
        );
    }

    /**
     * Construye las constantes utilizadas por el archivo generado.
     */
    private function buildConstants(
        ModuleData $module
    ): string {

        $constants = [

            sprintf(
                "public const MODULE = '%s';",
                $module->singular()
            ),

            sprintf(
                "public const GROUP = '%s';",
                $module->singular()
            ),

            sprintf(
                "public const GUARD = '%s';",
                'web'
            ),

        ];

        return implode(
            PHP_EOL,
            $constants
        );
    }
}
