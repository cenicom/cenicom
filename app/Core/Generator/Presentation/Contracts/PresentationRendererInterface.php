<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Contracts;

use App\Core\Generator\Presentation\DTO\InputPresentation;

interface PresentationRendererInterface
{
    /**
     * Renderiza una colección de presentaciones
     * convirtiéndolas en código Blade.
     *
     * @param InputPresentation[] $presentations
     *
     * @return string
     */
    public function render(
        array $presentations
    ): string;
}
