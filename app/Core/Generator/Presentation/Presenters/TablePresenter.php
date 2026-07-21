<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Presenters;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Contracts\PresentationInterface;
use App\Core\Generator\Presentation\DTO\TableColumnMetadata;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Presentador encargado de construir la representación
 * visual de las tablas del CN Generator.
 *
 * Transforma las columnas del módulo en una colección
 * de metadatos que posteriormente utilizará ViewGenerator
 * para generar automáticamente la vista index.
 *
 * No genera Blade.
 * No escribe archivos.
 * No depende de Laravel.
 *
 * @package App\Core\Generator\Presentation\Presenters
 * @since 2.0.0
 */
final readonly class TablePresenter implements PresentationInterface
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
     *
     * @return array<int, TableColumnMetadata>
     */
    public function present(): mixed
    {
        return $this->columns();
    }

    /**
     * Obtiene las columnas visibles de la tabla.
     *
     * @return array<int, TableColumnMetadata>
     */
    public function columns(): array
    {
        $columns = [];

        foreach ($this->module->columns() as $column) {

            if (! $column->shouldAppearInTable()) {
                continue;
            }

            if (! $column->type()->supportsTableColumn()) {
                continue;
            }

            $columns[] = $this->buildColumn($column);
        }

        return $columns;
    }

    /**
     * Construye los metadatos de una columna.
     */
    private function buildColumn(
        ColumnDefinition $column,
    ): TableColumnMetadata {

        return new TableColumnMetadata(

            name: $column->name(),

            label: $this->label($column),

            sortable: $this->sortable($column),

            searchable: $this->searchable($column),

            filterable: $this->filterable($column),

            alignment: $this->alignment($column),

            formatter: $this->formatter($column),

            attributes: $this->attributes($column),
        );
    }

    /**
     * Obtiene la etiqueta visible.
     */
    private function label(
        ColumnDefinition $column,
    ): string {

        return ucwords(
            str_replace('_', ' ', $column->name())
        );
    }

    /**
     * Determina si la columna puede ordenarse.
     */
    private function sortable(
        ColumnDefinition $column,
    ): bool {

        return $column->shouldBeSortable();
    }

    /**
     * Determina si la columna participa en búsquedas.
     */
    private function searchable(
        ColumnDefinition $column,
    ): bool {

        return $column->shouldBeSearchable();
    }

    /**
     * Determina si la columna participa en filtros.
     */
    private function filterable(
        ColumnDefinition $column,
    ): bool {

        return $column->shouldGenerateFilter();
    }

    /**
     * Obtiene la alineación sugerida.
     */
    private function alignment(
        ColumnDefinition $column,
    ): string {

        return match (true) {

            $column->type()->isNumeric() => 'end',

            $column->type()->isBoolean() => 'center',

            default => 'start',
        };
    }

    /**
     * Obtiene el formateador recomendado.
     */
    private function formatter(
        ColumnDefinition $column,
    ): ?string {

        return match (true) {

            $column->type()->isBoolean() => 'boolean',

            $column->type()->isDate() => 'date',

            $column->type()->isEnum() => 'badge',

            default => null,
        };
    }

    /**
     * Obtiene atributos adicionales de la columna.
     *
     * @return array<string,mixed>
     */
    private function attributes(
        ColumnDefinition $column,
    ): array {

        return [];
    }

    /**
     * Obtiene únicamente las etiquetas visibles.
     *
     * @return array<int,string>
     */
    public function labels(): array
    {
        return array_map(
            static fn(TableColumnMetadata $column): string => $column->label,
            $this->columns(),
        );
    }

    /**
     * Obtiene los nombres de las columnas.
     *
     * @return array<int,string>
     */
    public function names(): array
    {
        return array_map(
            static fn(TableColumnMetadata $column): string => $column->name,
            $this->columns(),
        );
    }

    /**
     * Obtiene toda la representación de la tabla.
     *
     * @return array<string,mixed>
     */
    public function metadata(): array
    {
        return [

            'columns' => $this->columns(),

            'labels' => $this->labels(),

            'names' => $this->names(),

        ];
    }
}
