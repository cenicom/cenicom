<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Value Object que describe la representación visual de un
 * componente del CN UI Framework.
 *
 * Esta clase actúa como puente entre el dominio
 * (InputType) y la capa de presentación del CN Generator.
 *
 * No contiene lógica de negocio ni dependencias de Laravel,
 * Blade o Bootstrap.
 *
 * Su única responsabilidad es transportar metadatos
 * necesarios para que los generadores construyan la interfaz.
 *
 * @package App\Core\Generator\Presentation\DTO
 * @since 2.0.0
 */
final readonly class ComponentMetadata
{
    public const COL_FULL = 'col-md-12';

    public const COL_HALF = 'col-md-6';

    public const COL_THIRD = 'col-md-4';

    public const COL_QUARTER = 'col-md-3';

    /**
     * Constructor.
     */
    public function __construct(

        /**
         * Nombre lógico del componente.
         *
         * Ejemplo:
         * input
         * textarea
         * select
         */
        public string $component,

        /**
         * Nombre del componente Blade.
         *
         * Ejemplo:
         * x-cn.input
         */
        public string $bladeComponent,

        /**
         * Clase CSS sugerida.
         */
        public string $cssClass,

        /**
         * Clase de layout sugerida.
         *
         * Ejemplo:
         * col-md-6
         */
        public string $columnClass,

        /**
         * Expresión Blade para enlazar valores.
         */
        public string $binding,

        /**
         * Icono sugerido.
         */
        public string $icon,

        /**
         * Placeholder recomendado.
         */
        public string $placeholder,

        /**
         * Atributos HTML adicionales.
         *
         * @var array<string,mixed>
         */
        public array $attributes = [],

    ) {}

    public function hasAttributes(): bool
    {
        return $this->attributes !== [];
    }

    public function blade(): string
    {
        return $this->bladeComponent;
    }
}
