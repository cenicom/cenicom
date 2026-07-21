<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Contracts;

/**
 * Contrato base para todos los Presenters del CN Generator.
 *
 * Un Presenter transforma objetos del dominio en objetos
 * de presentación utilizados por los generadores Blade.
 */
interface PresentationInterface
{
    /**
     * Genera la representación de presentación.
     */
    public function present(): mixed;
}
