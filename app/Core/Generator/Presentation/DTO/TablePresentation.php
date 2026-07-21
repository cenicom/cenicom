<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa la estructura completa de una tabla
 * del CN UI Framework.
 *
 * Contiene únicamente información de presentación.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */
final readonly class TablePresentation
{
    /**
     * @param array<int,TableColumnPresentation> $columns
     */
    public function __construct(

        /**
         * Columnas visibles.
         *
         * @var array<int,TableColumnPresentation>
         */
        public array $columns,

    ) {}

    /**
     * Devuelve las columnas.
     *
     * @return array<int,TableColumnPresentation>
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * Indica si existen columnas.
     */
    public function hasColumns(): bool
    {
        return $this->columns !== [];
    }
}
