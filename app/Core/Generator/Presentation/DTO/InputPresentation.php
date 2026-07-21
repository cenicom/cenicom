<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\DTO;

/**
 * Representación visual completa de un campo.
 */
final readonly class InputPresentation
{
    /**
     * Constructor.
     */
    public function __construct(

        public string $name,

        public string $label,

        public readonly string $type,

         public readonly string $placeholder,

        public ComponentMetadata $component,

        public bool $required,

        public bool $readonly,

        public bool $disabled,

        public mixed $default = null,

        public string $columnClass = 'col-md-6',

    ) {}

    /**
     * Indica si posee valor por defecto.
     */
    public function hasDefault(): bool
    {
        return $this->default !== null;
    }
}
