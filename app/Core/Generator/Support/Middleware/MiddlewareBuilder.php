<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Middleware;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * el Middleware del módulo.
 *
 * Responsabilidades:
 *
 * - Construir namespace.
 * - Construir imports.
 * - Construir nombre de la clase.
 * - Construir métodos del middleware.
 * - Preparar variables para el StubManager.
 *
 * MiddlewareGenerator únicamente orquesta el proceso.
 */
final class MiddlewareBuilder
{
    /**
     * Punto de entrada.
     *
     * Retorna todas las variables necesarias para el StubManager.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return $this->buildVariables($module);
    }

    /*
    |--------------------------------------------------------------------------
    | Variables del stub
    |--------------------------------------------------------------------------
    */

    /**
     * Construye todas las variables del stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(ModuleData $module): array
    {
        return [

            'namespace' => $this->buildNamespace($module),

            'imports' => $this->buildImports($module),

            'class' => $this->buildClassName($module),

            'methods' => $this->buildMiddlewareMethods($module),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Construcción de metadata
    |--------------------------------------------------------------------------
    */

    /**
     * Construye el namespace.
     */
    private function buildNamespace(ModuleData $module): string
    {
        return $module->middlewareNamespace();
    }

    /**
     * Construye los imports.
     */
    private function buildImports(ModuleData $module): string
    {
        $imports = [
            'use Closure;',
            'use Illuminate\Http\Request;',
            'use Symfony\Component\HttpFoundation\Response;',
        ];

        // Imports futuros según opciones del módulo
        // if ($module->hasPermissions()) {
        //     $imports[] = 'use Illuminate\Support\Facades\Auth;';
        // }

        return implode("\n", array_unique($imports));
    }

    /**
     * Construye el nombre de la clase.
     */
    private function buildClassName(ModuleData $module): string
    {
        return $module->middlewareClass();
    }

    /*
    |--------------------------------------------------------------------------
    | Construcción del middleware
    |--------------------------------------------------------------------------
    */

    /**
     * Construye los métodos del middleware.
     */
    private function buildMiddlewareMethods(ModuleData $module): string
    {
        return implode("\n\n", array_filter([

            $this->buildHandleMethod($module),

            // futuros métodos...

        ]));
    }

    /**
     * Construye el método handle().
     */
    private function buildHandleMethod(ModuleData $module): string
    {
        return <<<PHP
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  \$request
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  \$next
         */
        public function handle(Request \$request, Closure \$next): Response
        {
            {$this->indent($this->buildAuthorization($module), 2)}
        }
    PHP;
    }

    /**
     * Construye el código de autorización.
     */
    private function buildAuthorization(ModuleData $module): string
    {
        return implode("\n\n", [

            $this->buildPermissionCheck($module),

            $this->buildReturnStatement(),

        ]);
    }

    /**
     * Construye la validación del permiso principal.
     */
    private function buildPermissionCheck(
        ModuleData $module,
    ): string {
        $permission = $this->resolvePrimaryPermission($module);

        if ($permission === null) {
            return '// El módulo no define permisos.';
        }

        return sprintf(
            "if (%s) {\n    %s\n}",
            $this->buildPermissionCondition($permission),
            $this->buildUnauthorizedResponse()
        );
    }

    /**
     * Construye la respuesta cuando el usuario
     * no posee el permiso requerido.
     */
    private function buildUnauthorizedResponse(): string
    {
        return $this->buildWebUnauthorizedResponse();

    }

    /**
     * Construye la respuesta para peticiones web.
     */
    private function buildWebUnauthorizedResponse(): string
    {
        return 'abort(403);';
    }

    private function buildReturnStatement(): string
    {
        return 'return $next($request);';
    }

    /**
     * Construye la condición de autorización.
     */
    private function buildPermissionCondition(
        string $permission,
    ): string {
        return sprintf(
            "! \$request->user()?->can('%s')",
            $permission
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function indent(string $text, int $level = 1): string
    {
        $indent = str_repeat('    ', $level);

        return implode(
            "\n",
            array_map(
                fn($line) => $indent . $line,
                explode("\n", $text)
            )
        );
    }

    /**
     * Obtiene el permiso principal del módulo.
     */
    private function resolvePrimaryPermission(
        ModuleData $module,
    ): ?string {
        $matrix = $module->permissionMatrix();

        if ($matrix === null) {
            return null;
        }

        foreach ($matrix->permissions() as $permission) {

            if ($permission->action() === 'view') {
                return $permission->permission();
            }
        }

        return null;
    }
}
