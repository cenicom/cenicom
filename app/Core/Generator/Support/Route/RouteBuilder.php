<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Route;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Support\MiddlewareResolver;
use App\Core\Generator\Security\PermissionResolver;

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
        private readonly PermissionResolver $permissionResolver,
    ) {
    }


    /**
     * Punto de entrada.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module ): array {

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

        'controllerNamespace'
            => $module->qualifiedController(),

        'controllerClass'
            => $module->controllerClass(),

        'plural'
            => $module->plural(),

        'singular'
            => $module->singular(),

        'middleware'
            => $this->buildMiddleware($module),
    ];
    }


    private function buildControllerNamespace(
        ModuleData $module
    ): string {

        return $module->qualifiedController();
    }


    private function buildControllerClass(
        ModuleData $module
    ): string {

        return $module->controllerClass();
    }


    /**
     * Construye middleware dinámico.
     */
    private function buildMiddleware(
        ModuleData $module
    ): string {

        $security = $module->security();

        if ($security === null) {
            return '';
        }


        $middlewares = $this
            ->middlewareResolver
            ->resolve($security);


        if ($security->usesPermissions()) {

            foreach (
                $this->permissionResolver->resolve($module)
                as $permission
            ) {

                $middlewares[] =
                    'permission:' . $permission;
            }
        }


        return $this->formatMiddleware(
            $middlewares
        );
    }


    /**
     * Formatea middleware para Route.
     *
     * @param array<int,string> $middlewares
     */
    private function formatMiddleware(
        array $middlewares
    ): string {

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
