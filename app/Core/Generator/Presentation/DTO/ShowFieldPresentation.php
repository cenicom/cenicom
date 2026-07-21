<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa un campo de la vista Show.
 *
 * Contiene únicamente información de presentación.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */
final readonly class ShowFieldPresentation
{
    /**
     * Constructor.
     */
    public function __construct(

        /**
         * Nombre del atributo.
         */
        public string $name,

        /**
         * Texto mostrado al usuario.
         */
        public string $label,

        /**
         * Expresión Blade utilizada para mostrar el valor.
         *
         * Ejemplo:
         * $currency->name
         */
        public string $binding,

        /**
         * Clase CSS opcional.
         */
        public string $cssClass = '',

        /**
         * Clase del layout.
         */
        public string $columnClass = 'col-md-6',

    ) {}
}
