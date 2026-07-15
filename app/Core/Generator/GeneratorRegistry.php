<?php

declare(strict_types=1);

namespace App\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Registro de generadores disponibles para el CN Generator.
 *
 * Centraliza la colección de generadores que serán utilizados
 * por ModuleGenerator durante el proceso de generación.
 *
 * @package App\Core\Generator
 * @since 1.0.0
 */
final readonly class GeneratorRegistry
{
    /**
     * @param iterable<GeneratorInterface> $generators
     */
    public function __construct(
        private iterable $generators,
    ) {
    }

    /**
     * Devuelve todos los generadores registrados.
     *
     * @return iterable<GeneratorInterface>
     */
    public function all(): iterable
    {
        return $this->generators;
    }
}
