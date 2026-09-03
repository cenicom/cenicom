<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Support\MiddlewareResolver;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias
 * para generar las rutas del módulo.
 *
 * RouteGenerator únicamente orquesta.
 */
final class RouteBuilder
{
    public function __construct(
        private readonly MiddlewareResolver $middlewareResolver,
    ) {}

    /**
     * Punto de entrada.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return $this->buildVariables($module);
    }

    /**
     * Construye variables del stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(ModuleData $module): array
    {
        return [
            'qualifiedController'
            => $module->qualifiedController(),

            'controllerClass'
            => $module->controllerClass(),

            'routeResource'
            => $module->routeResource(),

            'routeName'
            => $module->routeName(),

            'plural'
            => $module->plural(),

            'singular'
            => $module->singular(),

            'middleware'
            => $this->buildMiddleware($module),
        ];
    }

    /**
     * Construye middleware dinámico.
     */
    private function buildMiddleware(ModuleData $module): string
    {
        $security = $module->security();

        if ($security === null) {
            return '';
        }

        $middlewares = $this
            ->middlewareResolver
            ->resolve($security);

        return $this->formatMiddleware($middlewares)
            . $this->buildCrudMiddleware($module);
    }

    private function buildCrudMiddleware(ModuleData $module): string
    {
        $security = $module->security();

        if (
            $security === null
            || ! $security->usesPermissions()
        ) {
            return '';
        }

        $middleware = [];

        foreach (
            $module->permissionMatrix()->permissions()
            as $permission
        ) {
            if (
                ! $permission->isEnabled()
                || ! $permission->shouldGenerateMiddleware()
            ) {
                continue;
            }

            $permissionName = 'permission:' . $permission->permission();

            match ($permission->action()) {
                'view' => $middleware[] =
                    "->middlewareFor(['index', 'show'], '{$permissionName}')",

                'create' => $middleware[] =
                    "->middlewareFor(['create', 'store'], '{$permissionName}')",

                'update' => $middleware[] =
                    "->middlewareFor(['edit', 'update'], '{$permissionName}')",

                'delete' => $middleware[] =
                    "->middlewareFor('destroy', '{$permissionName}')",

                default => null,
            };
        }

        return $middleware === []
            ? ''
            : PHP_EOL . implode(PHP_EOL, $middleware);
    }

    /**
     * Formatea middleware para Route.
     *
     * @param array<int,string> $middlewares
     */
    private function formatMiddleware(array $middlewares): string
    {
        if ($middlewares === []) {
            return '';
        }

        return PHP_EOL .
            "->middleware([" .
            PHP_EOL .
            "    '" .
            implode(
                "'," . PHP_EOL . "    '",
                $middlewares
            ) .
            "'" .
            PHP_EOL .
            "])";
    }
}
