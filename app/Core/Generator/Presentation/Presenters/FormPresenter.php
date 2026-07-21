<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Presenters;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Contracts\PresentationInterface;
use App\Core\Generator\Presentation\DTO\ComponentMetadata;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Presentador encargado de construir la representación
 * visual completa de un formulario.
 *
 * Orquesta la conversión de todas las columnas del módulo
 * hacia ComponentMetadata utilizando ColumnPresenter.
 *
 * No genera Blade.
 * No conoce Laravel.
 * No escribe archivos.
 *
 * Su única responsabilidad consiste en preparar la colección
 * de componentes que posteriormente utilizará ViewGenerator.
 *
 * @package App\Core\Generator\Presentation\Presenters
 * @since 2.0.0
 */
final readonly class FormPresenter implements PresentationInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private ModuleData $module,
    ) {}

    /**
     * Obtiene el módulo.
     */
    public function module(): ModuleData
    {
        return $this->module;
    }

    /**
     * {@inheritDoc}
     */
    public function present(): mixed
    {
        return $this->sections();
    }

    /**
     * Obtiene la colección de componentes del formulario.
     *
     * @return array<int, ComponentMetadata>
     */
    public function fields(): array
    {
        $fields = [];

        foreach ($this->module->columns() as $column) {

            if (! $column->shouldAppearInForm()) {
                continue;
            }

            $fields[] = (new ColumnPresenter($column))
                ->present();
        }

        return $fields;
    }

    /**
     * Obtiene el formulario organizado por filas.
     *
     * @return array<int,array<int,ComponentMetadata>>
     */
    public function rows(): array
    {
        return $this->buildRows(
            $this->fields()
        );
    }

    /**
     * Organiza los componentes por filas Bootstrap.
     *
     * @param array<int,ComponentMetadata> $fields
     * @return array<int,array<int,ComponentMetadata>>
     */
    private function buildRows(array $fields): array
    {
        $rows = [];

        $currentRow = [];

        $currentWidth = 0;

        foreach ($fields as $field) {

            $width = $this->columnWidth(
                $field->columnClass
            );

            if (($currentWidth + $width) > 12) {

                $rows[] = $currentRow;

                $currentRow = [];

                $currentWidth = 0;
            }

            $currentRow[] = $field;

            $currentWidth += $width;
        }

        if ($currentRow !== []) {
            $rows[] = $currentRow;
        }

        return $rows;
    }

    /**
     * Obtiene el ancho Bootstrap.
     */
    private function columnWidth(
        string $columnClass
    ): int {

        return match ($columnClass) {

            ComponentMetadata::COL_FULL => 12,

            ComponentMetadata::COL_HALF => 6,

            ComponentMetadata::COL_THIRD => 4,

            ComponentMetadata::COL_QUARTER => 3,

            default => 12,
        };
    }

    /**
     * Obtiene las secciones del formulario.
     *
     * @return array<int,array<string,mixed>>
     */
    public function sections(): array
    {
        return [
            $this->buildSection(
                'General',
                $this->rows(),
            ),
        ];
    }

    /**
     * Construye una sección del formulario.
     *
     * @param array<int,array<int,ComponentMetadata>> $rows
     *
     * @return array<string,mixed>
     */
    private function buildSection(
        string $title,
        array $rows,
    ): array {

        return [

            'title' => $title,

            'rows' => $rows,

        ];
    }

    /**
     * Obtiene toda la representación del formulario.
     *
     * @return array<string,mixed>
     */
    public function metadata(): array
    {
        return [

            'fields' => $this->fields(),

            'rows' => $this->rows(),

            'sections' => $this->sections(),

        ];
    }
}
