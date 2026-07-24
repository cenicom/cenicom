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

    /**
     * Construye el namespace.
     */
    private function buildNamespace(ModuleData $module): string
    {
        return $module->middlewareNamespace();
    }

    /**
     * Construye el nombre de la clase.
     */
    private function buildClassName(ModuleData $module): string
    {
        return $module->middlewareClass();
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
            '// TODO: Implementar autorización del middleware.',
            $this->buildReturnStatement(),
        ]);
    }

    /**
     * Construye el retorno del middleware.
     */
    private function buildReturnStatement(): string
    {
        return 'return $next($request);';
    }

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
}
