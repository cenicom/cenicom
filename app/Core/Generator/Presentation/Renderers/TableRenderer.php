<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Renderers;

use App\Core\Generator\Presentation\DTO\TablePresentation;
use App\Core\Generator\Support\StubManager;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Renderiza la estructura de una tabla utilizando
 * stubs Blade.
 *
 * Su única responsabilidad consiste en transformar un
 * TablePresentation en código Blade.
 *
 * No conoce ModuleData.
 * No escribe archivos.
 * No contiene lógica de negocio.
 *
 * @package App\Core\Generator\Presentation\Renderers
 * @since 2.0.0
 */
final readonly class TableRenderer
{
    /**
     * Stub utilizado para una columna.
     */
    private const STUB_COLUMN = 'components/table/column.stub';

    public function __construct(
        private StubManager $stubManager,
    ) {}

    /**
     * Renderiza todas las columnas.
     */
    public function render(
        TablePresentation $table,
    ): string {

        $columns = [];

        foreach ($table->columns() as $column) {

            $columns[] = $this->renderColumn($column);
        }

        return implode(
            PHP_EOL,
            $columns,
        );
    }

    /**
     * Renderiza una columna.
     */
    private function renderColumn(
        \App\Core\Generator\Presentation\DTO\TableColumnPresentation $column,
    ): string {

        return $this->stubManager->render(
            self::STUB_COLUMN,
            [
                'name' => $column->name,
                'label' => $column->label,
                'css_class' => $column->cssClass,
                'alignment' => $column->alignment,
            ],
        );
    }
}
