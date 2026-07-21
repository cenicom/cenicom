<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation;

use App\Core\Generator\Enums\InputType;
use App\Core\Generator\Presentation\DTO\ComponentMetadata;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Adaptador entre InputType y ComponentMetadata.
 *
 * Su única responsabilidad es transformar un InputType
 * en un DTO de presentación consumido por los generadores.
 *
 * No contiene reglas de negocio.
 * No conoce HTML.
 * No conoce Blade.
 * No mantiene catálogos propios.
 */
final readonly class InputPresentation
{
    /**
     * Convierte un InputType en un ComponentMetadata.
     *
     * Este método constituye el único punto autorizado para
     * obtener la representación visual de un tipo de entrada.
     */
    public function for(InputType $type): ComponentMetadata
    {
        return new ComponentMetadata(

            component: $type->componentName(),

            bladeComponent: $type->bladeComponentTag(),

            cssClass: $type->defaultCssClass(),

            columnClass: $type->preferredColumnWidth(),

            binding: $type->defaultBladeBinding(),

            icon: $type->defaultIcon(),

            placeholder: $type->defaultPlaceholder(),

            attributes: $type->defaultAttributes(),

        );
    }
}
