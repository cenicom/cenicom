<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa la vista Show completa.
 *
 * Contiene únicamente metadatos de presentación.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */
final readonly class ShowPresentation
{
    /**
     * @param array<int,ShowFieldPresentation> $fields
     */
    public function __construct(

        /**
         * Campos visibles.
         *
         * @var array<int,ShowFieldPresentation>
         */
        public array $fields,

    ) {}

    /**
     * Devuelve los campos.
     *
     * @return array<int,ShowFieldPresentation>
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * Indica si existen campos.
     */
    public function hasFields(): bool
    {
        return $this->fields !== [];
    }
}
