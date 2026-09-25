<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa un campo de la vistas.
 *
 * Contiene únicamente información de presentación.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */

final readonly class FieldPresentationMetadata
{
    public function __construct(
        public string $label,
    ) {}
}
