<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
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
final class ViewGenerator extends BaseGenerator
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
        StubManager $stubManager,
        FileWriter $fileWriter,
        private readonly PresentationFactory $presentationFactory,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
        );
    }

    public function supports(
        ModuleData $module
    ): bool {
        return true;
    }

    public function generate(
        ModuleData $module,
    ): GeneratorResult {

        $result = new GeneratorResult();

        $variables = $this->buildVariables(
            $module
        );

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
     * Genera una vista Blade individual.
     *
     * @param array<string,mixed> $view
     * @param array<string,mixed> $variables
     */
    private function generateView(
        ModuleData $module,
        array $view,
        array $variables,
        GeneratorResult $result,
    ): void {

        $path = $module->viewPath()
            . DIRECTORY_SEPARATOR
            . $view[self::KEY_TARGET];

        $generated = $this->generateFile(
            $view[self::KEY_STUB],
            $path,
            $variables
        );

        $result->addGenerated(
            $path,
            $generated,
        );
    }

    /**
     * Construye todas las variables utilizadas por los stubs.
     *
     * @return array<string,mixed>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        $domain = $this->buildDomainVariables(
            $module
        );

        $presentation = $this->buildPresentationVariables(
            $module
        );

        return array_merge(
            $domain,
            $presentation,
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

    /**
     * Construye las variables de presentación.
     *
     * @return array<string,mixed>
     */
    private function buildPresentationVariables(
        ModuleData $module,
    ): array {

        $form = $this->presentationFactory
            ->form($module)
            ->metadata();

        $table = $this->presentationFactory
            ->table($module)
            ->metadata();

        return [

            /*
    |--------------------------------------------------------------------------
    | Formulario
    |--------------------------------------------------------------------------
    */

            'form_fields' => $this->buildFormFields($form),

            'form_component' => $this->buildFormComponent($form),


            /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

            'table_headers' => $this->buildTableHeaders($table),

            'table_columns' => $this->buildTableColumns($table),


            /*
    |--------------------------------------------------------------------------
    | Información general
    |--------------------------------------------------------------------------
    */

            'module_name' => $module->name(),

            'model_class' => $module->modelClass(),

        ];
    }
}
