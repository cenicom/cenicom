<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Presentation\Renderers\TableRenderer;
use App\Core\Generator\Presentation\Renderers\ShowRenderer;

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
    private const KEY_STUB = 'stub';

    private const KEY_TARGET = 'target';

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
        private readonly FileWriter $fileWriter,
        private readonly PresentationFactory $presentationFactory,
        private readonly ComponentRenderer $componentRenderer,
        private readonly TableRenderer $tableRenderer,
        private readonly ShowRenderer $showRenderer,
    ) {}

    public function supports(
        ModuleData $module
    ): bool {
        return true;
    }

    public function generate(
        ModuleData $module,
    ): GeneratorResult {

        $form = $this->presentationFactory->form($module);

        $table = $this->presentationFactory->table($module);

        $show = $this->presentationFactory->show($module);

        $formFields = [];

        foreach ($form->fields() as $input) {
            $formFields[] = $this->componentRenderer->render($input);
        }

        $variables = $this->buildVariables($module);

        $variables['form_fields'] = implode(
            PHP_EOL . PHP_EOL,
            $formFields,
        );

        $variables['table_columns'] =
            $this->tableRenderer->render(
                $table,
            );

        $variables['columns'] =
            $this->showRenderer->render(
                $show,
            );

        return $this->generateViews(
            $module,
            $variables,
        );
    }

    /**
     * Genera todas las vistas del módulo.
     */
    /**
     * Genera todas las vistas del módulo.
     */
    private function generateViews(
        ModuleData $module,
        array $variables,
    ): GeneratorResult {

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
            $view[self::KEY_STUB],
            $variables,
        );

        $path = $module->viewPath()
            . DIRECTORY_SEPARATOR
            . $view[self::KEY_TARGET];

        $this->fileWriter->write(
            $path,
            $content,
        );

        $result->addCreated($path);
    }

    /**
     * Construye todas las variables utilizadas por los stubs.
     *
     * @return array<string,mixed>
     */
    /**
     * Construye todas las variables utilizadas por los stubs.
     *
     * @return array<string,mixed>
     */
    private function buildVariables(
        ModuleData $module,
    ): array {

        return $this->buildDomainVariables(
            $module,
        );
    }

    /**
     * Construye las variables provenientes del dominio.
     *
     * @return array<string,mixed>
     */
    private function buildDomainVariables(
        ModuleData $module,
    ): array {
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
