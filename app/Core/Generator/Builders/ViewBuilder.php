<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Presentation\Renderers\ShowRenderer;
use App\Core\Generator\Presentation\Renderers\TableRenderer;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * las vistas Blade de un módulo.
 *
 * Responsabilidades:
 *
 * - Construir variables de dominio.
 * - Construir campos del formulario.
 * - Construir columnas de tabla.
 * - Construir columnas de detalle.
 * - Preparar variables para los stubs de vistas.
 */
final class ViewBuilder
{
    public function __construct(
        private readonly PresentationFactory $presentationFactory,
        private readonly ComponentRenderer $componentRenderer,
        private readonly TableRenderer $tableRenderer,
        private readonly ShowRenderer $showRenderer,
    ) {}

    /**
     * Construye todas las variables necesarias para los stubs.
     *
     * @return array<string,mixed>
     */
    public function build(ModuleData $module): array
    {
        $form = $this->presentationFactory->form($module);
        $table = $this->presentationFactory->table($module);
        $show = $this->presentationFactory->show($module);

        $formFields = [];

        foreach ($form->fields() as $input) {
            $formFields[] = $this->componentRenderer->render($input);
        }

        return array_merge(
            $this->buildDomainVariables($module),
            [
                'form_fields' => implode(
                    PHP_EOL . PHP_EOL,
                    $formFields
                ),

                'table_columns' => $this->tableRenderer->render(
                    $table
                ),

                'columns' => $this->showRenderer->render(
                    $show
                ),
            ]
        );
    }

    /**
     * Construye las variables provenientes del dominio.
     *
     * @return array<string,mixed>
     */
    private function buildDomainVariables(ModuleData $module): array
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
                    fn ($column) => $column->shouldAppearInTable()
                )
            ) + 1,
        ];
    }
}
