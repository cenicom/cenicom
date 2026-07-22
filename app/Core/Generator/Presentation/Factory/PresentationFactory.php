<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Factory;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;

use App\Core\Generator\Presentation\Presenters\ColumnPresenter;
use App\Core\Generator\Presentation\Presenters\FormPresenter;

use App\Core\Generator\Presentation\DTO\TablePresentation;
use App\Core\Generator\Presentation\DTO\TableColumnPresentation;
use App\Core\Generator\Presentation\DTO\ShowPresentation;
use App\Core\Generator\Presentation\DTO\ShowFieldPresentation;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory responsable de construir los presenters utilizados
 * por la capa Presentation del CN Generator.
 *
 * Centraliza la creación de todos los objetos de presentación,
 * evitando que los generadores dependan directamente de las
 * implementaciones concretas.
 *
 * Esta clase constituye el único punto oficial para instanciar
 * presenters dentro del sistema.
 *
 * No contiene lógica de negocio.
 * No genera Blade.
 * No escribe archivos.
 *
 * @package App\Core\Generator\Presentation
 * @since 2.0.0
 */
final readonly class PresentationFactory
{
    /**
     * Construye un FormPresenter.
     */
    public function form(
        ModuleData $module,
    ): FormPresenter {
        return new FormPresenter($module);
    }

    /**
     * Construye la presentación de la tabla.
     */
    public function table(
        ModuleData $module,
    ): TablePresentation {

        $columns = [];

        foreach ($module->columns() as $column) {

            if (! $column->shouldAppearInTable()) {
                continue;
            }

            $columns[] = new TableColumnPresentation(
                name: $column->name(),
                label: $this->makeLabel(
                    $column->name(),
                ),
            );
        }

        return new TablePresentation(
            columns: $columns,
        );
    }

    /**
     * Convierte un nombre de campo en una etiqueta legible.
     */
    private function makeLabel(
        string $name,
    ): string {

        return ucwords(
            str_replace(
                '_',
                ' ',
                $name,
            ),
        );
    }

    /**
     * Construye un ColumnPresenter.
     */
    public function column(
        ColumnDefinition $column,
    ): ColumnPresenter {
        return new ColumnPresenter($column);
    }

    /**
     * Construye la presentación de la vista Show.
     */
    public function show(
        ModuleData $module,
    ): ShowPresentation {

        $fields = [];

        foreach ($module->columns() as $column) {

            if (! $column->shouldAppearInShow()) {
                continue;
            }

            $fields[] = new ShowFieldPresentation(

                name: $column->name(),

                label: $this->makeLabel(
                    $column->name(),
                ),

                binding: sprintf(
                    '$%s->%s',
                    $module->variable(),
                    $column->name(),
                ),

            );
        }

        return new ShowPresentation(
            fields: $fields,
        );
    }
}
