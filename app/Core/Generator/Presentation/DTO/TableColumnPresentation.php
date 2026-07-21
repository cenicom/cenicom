<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa una columna de una tabla del CN UI Framework.
 *
 * Es un DTO inmutable utilizado por TableRenderer para
 * construir el encabezado de las tablas Blade.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */
final readonly class TableColumnPresentation
{
    /**
     * Constructor.
     */
    public function __construct(

        /**
         * Nombre del campo.
         */
        public string $name,

        /**
         * Texto visible en la cabecera.
         */
        public string $label,

        /**
         * Indica si la columna debe aparecer
         * en el listado.
         */
        public bool $visible = true,

        /**
         * Clase CSS opcional.
         */
        public string $cssClass = '',

        /**
         * Alineación.
         */
        public string $alignment = 'start',

    ) {}
}
