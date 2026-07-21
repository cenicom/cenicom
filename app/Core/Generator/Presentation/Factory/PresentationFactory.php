<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Factory;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;

use App\Core\Generator\Presentation\Presenters\ColumnPresenter;
use App\Core\Generator\Presentation\Presenters\FormPresenter;
use App\Core\Generator\Presentation\Presenters\TablePresenter;

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
     * Construye un TablePresenter.
     */
    public function table(
        ModuleData $module,
    ): TablePresenter {
        return new TablePresenter($module);
    }

    /**
     * Construye un ColumnPresenter.
     */
    public function column(
        ColumnDefinition $column,
    ): ColumnPresenter {
        return new ColumnPresenter($column);
    }
}
