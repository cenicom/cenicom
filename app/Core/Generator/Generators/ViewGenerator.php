<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Builders\ViewBuilder;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\Contracts\FileWriterInterface;
use App\Core\Generator\Support\StubManager;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Generador encargado de construir automáticamente las vistas
 * Blade de un módulo del CN Generator.
 *
 * Este generador consume la capa Presentation para transformar
 * la información del módulo en las vistas del CN UI Framework.
 *
 * Es responsable de generar:
 *
 * - index.blade.php
 * - create.blade.php
 * - edit.blade.php
 * - show.blade.php
 * - form.blade.php
 * - partials
 *
 * No contiene lógica de presentación.
 * Toda la representación visual es delegada a los Presenters.
 *
 * @package App\Core\Generator\Generators
 * @since 2.0.0
 */
final class ViewGenerator implements GeneratorInterface
{
    private const VIEW__STUB = 'stub';

    private const VIEW__TARGET = 'target';

    private const VIEWS = [

        [
            'stub' => 'views/index.stub',
            'target' => 'index.blade.php',
        ],

        [
            'stub' => 'views/create.stub',
            'target' => 'create.blade.php',
        ],

        [
            'stub' => 'views/edit.stub',
            'target' => 'edit.blade.php',
        ],

        [
            'stub' => 'views/show.stub',
            'target' => 'show.blade.php',
        ],

        [
            'stub' => 'views/_form.stub',
            'target' => '_form.blade.php',
        ],

        [
            'stub' => 'views/export.stub',
            'target' => 'export.blade.php'
        ]

    ];

    /**
     * Obtiene la lista de vistas a generar.
     *
     * @return array<int,array<string,string>>
     */
    private function views(): array
    {
        return self::VIEWS;
    }

    public function __construct(
        private readonly StubManager $stubManager,
        private readonly FileWriterInterface $fileWriter,
        private readonly ViewBuilder $builder,
    ) {}

    public function supports(ModuleData $module): bool
    {
        return true;
    }

    public function generate(ModuleData $module): GeneratorResult
    {
        $variables = $this->builder->build($module);

        return $this->generateViews(
            $module,
            $variables,
        );
    }

    /**
     * Genera todas las vistas del módulo.
     */
    private function generateViews(ModuleData $module, array $variables,): GeneratorResult
    {

        $result = new GeneratorResult();

        foreach ($this->views() as $view) {

            $this->generateView(
                $module,
                $view,
                $variables,
                $result,
            );
        }

        return $result;
    }

    /**
     * Genera una vista individual.
     *
     * @param array<string,string> $view
     * @param array<string,mixed>  $variables
     */
    private function generateView(
        ModuleData $module,
        array $view,
        array $variables,
        GeneratorResult $result,
    ): void {

        $content = $this->stubManager->render(
            $view[self::VIEW__STUB],
            $variables,
        );

        $path = $module->viewPath()
            . DIRECTORY_SEPARATOR
            . $view[self::VIEW__TARGET];

        try {

            $this->fileWriter->write(
                $path,
                $content,
            );

            $result->addCreated($path);
        } catch (\Throwable $exception) {
            $result->addError(
                sprintf(
                    '[%s] %s',
                    $path,
                    $exception->getMessage()
                )
            );
        }
    }

    /**
     * Construye todas las variables utilizadas por los stubs.
     *
     * @return array<string,mixed>
     */
    private function buildVariables(ModuleData $module,): array
    {
        return $this->buildDomainVariables(
            $module,
        );
    }

    /**
     * Construye las variables provenientes del dominio.
     *
     * @return array<string,mixed>
     */
    private function buildDomainVariables(ModuleData $module,): array
    {
        return [

            'title' => $module->plural(),

            'description' => $module->description(),

            'model' => $module->variable(),

            'modelClass' => $module->modelClass(),

            'singular' => $module->singular(),

            'plural' => $module->plural(),

            'routePrefix' => $module->routePrefix(),

            'routeName' => $module->routeName(),

            'viewPrefix' => $module->viewPrefix(),

            'table' => $module->table(),

            'fields' => $module->columns(),

            'collection' => $module->pluralVariable(),

            'columnCount' => count(
                array_filter(
                    $module->columns(),
                    fn($column) => $column->shouldAppearInTable()
                )
            ) + 1,

        ];
    }
}
