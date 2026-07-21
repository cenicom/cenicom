<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Value Object que representa los metadatos de una columna
 * de tabla generada por el CN Generator.
 *
 * Actúa como puente entre ColumnDefinition y el
 * ViewGenerator, encapsulando toda la información necesaria
 * para construir automáticamente tablas del CN UI Framework.
 *
 * No contiene lógica de negocio.
 * No depende de Laravel.
 * No genera Blade.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */
final readonly class TableColumnMetadata
{
    /**
     * Constructor.
     */
    public function __construct(

        /**
         * Nombre de la columna.
         */
        public string $name,

        /**
         * Etiqueta visible.
         */
        public string $label,

        /**
         * Permite ordenamiento.
         */
        public bool $sortable = true,

        /**
         * Permite búsqueda.
         */
        public bool $searchable = true,

        /**
         * Permite filtrado.
         */
        public bool $filterable = true,

        /**
         * Alineación sugerida.
         *
         * start|center|end
         */
        public string $alignment = 'start',

        /**
         * Formateador sugerido.
         *
         * boolean
         * badge
         * date
         * currency
         * etc.
         */
        public ?string $formatter = null,

        /**
         * Atributos adicionales.
         *
         * @var array<string,mixed>
         */
        public array $attributes = [],

    ) {
    }

    /**
     * Determina si existen atributos adicionales.
     */
    public function hasAttributes(): bool
    {
        return $this->attributes !== [];
    }

    /**
     * Determina si la columna utiliza un formateador.
     */
    public function hasFormatter(): bool
    {
        return $this->formatter !== null;
    }

    /**
     * Indica si la columna puede ordenarse.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Indica si la columna participa en búsquedas.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Indica si la columna participa en filtros.
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }
}
