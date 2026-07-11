<?php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Contrato base para todos los casos de uso del sistema.
 *
 * Una Action encapsula una única operación de negocio
 * (caso de uso) y puede recibir uno o más argumentos.
 */
interface ActionInterface
{
    /**
     * Ejecuta el caso de uso.
     *
     * @param mixed ...$arguments
     */
    public function execute(mixed ...$arguments): mixed;
}
