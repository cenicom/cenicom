<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye la definición de rutas de un módulo.
 *
 * Esta clase encapsula toda la lógica relacionada con la
 * generación de grupos de rutas, middleware, prefijos y
 * nombres, manteniendo el RouteGenerator completamente
 * desacoplado de dicha lógica.
 *
 * @package App\Core\Generator\Builders
 * @since 1.0.0
 */
final class RouteBuilder
{
    /**
     * Construye el bloque de rutas.
     */
    public function build(
        ModuleData $module
    ): string {

        $lines = [];

        /*
        |--------------------------------------------------------------------------
        | Middleware
        |--------------------------------------------------------------------------
        */

        if ($module->routeMiddleware() !== []) {

            $middleware = implode(
                "', '",
                $module->routeMiddleware()
            );

            $lines[] =
                "Route::middleware(['{$middleware}'])";
        }

        /*
        |--------------------------------------------------------------------------
        | Prefix
        |--------------------------------------------------------------------------
        */

        if ($module->routePrefix() !== '') {

            $this->append(
                $lines,
                "->prefix('{$module->routePrefix()}')"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Name Prefix
        |--------------------------------------------------------------------------
        */

        if ($module->routeNamePrefix() !== '') {

            $this->append(
                $lines,
                "->name('{$module->routeNamePrefix()}.')"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Group
        |--------------------------------------------------------------------------
        */

        if ($lines !== []) {

            $lines[] = '->group(function (): void {';

            $lines[] =
                '    ' .
                $this->resourceRoute($module);

            $lines[] = '});';

            return implode("\n", $lines);
        }

        return $this->resourceRoute($module);
    }

    /**
     * Construye el Route::resource().
     */
    private function resourceRoute(
        ModuleData $module
    ): string {

        return sprintf(

            "Route::resource('%s', %s::class)->names('%s');",

            $module->resourceUri(),

            $module->controllerClass(),

            $module->routeName()

        );
    }

    /**
     * Agrega una línea al builder.
     *
     * @param array<int,string> $lines
     */
    private function append(
        array &$lines,
        string $line
    ): void {

        if ($lines === []) {

            $lines[] = $line;

            return;
        }

        $lines[count($lines) - 1] .= $line;
    }
}
