<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;


use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\MiddlewareResolver;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente las rutas de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class RouteGenerator extends BaseGenerator
{
    private const STUB = 'route.stub';

     public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly MiddlewareResolver $middlewareResolver,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    public function supports(ModuleData $module): bool
    {
        return true;
    }



    /**
     * Genera las rutas del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        return $this->generateResult(
            'route.stub',
            $module->routePath(),
            $this->buildVariables($module)
        );
    }

    /**
     * Construye las variables utilizadas por el stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        return array_merge(
            $this->defaultVariables($module),
            [
                'controllerNamespace'
                => $module->qualifiedController(),

                'controllerClass'
                => $module->controllerClass(),

                'pluralVariable'
                => $module->pluralVariable(),

                'middleware'
                => $this->buildMiddleware($module),
            ]
        );
    }

    /**
     * Construye el bloque middleware de la ruta.
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

        return $this->formatMiddleware($middlewares);
    }

    /**
     * Formatea middleware para el stub de rutas.
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
            '->middleware([' .
            PHP_EOL .
            '    \'' .
            implode(
                "'," . PHP_EOL . "    '",
                $middlewares
            ) .
            '\'' .
            PHP_EOL .
            '])';
    }
}
