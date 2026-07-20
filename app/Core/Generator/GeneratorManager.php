<?php

declare(strict_types=1);

namespace App\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Corazón del CN Generator.
 *
 * Orquesta la ejecución de todos los generadores registrados
 * para producir automáticamente un módulo completo.
 *
 * Responsabilidades:
 *
 * - Registrar generadores
 * - Ejecutarlos
 * - Consolidar resultados
 * - Finalizar la generación cuando un generador indique
 *   que no puede continuar.
 *
 * No conoce:
 *
 * - Stubs
 * - FileWriter
 * - PathResolver
 * - NamespaceResolver
 * - Implementaciones concretas
 *
 * @author CENICOM
 */
final class GeneratorManager
{
    /**
     * Generadores registrados.
     *
     * @var array<GeneratorInterface>
     */
    private array $generators;

    /**
     * Constructor.
     *
     * @param iterable<GeneratorInterface> $generators
     */
    public function __construct(
        iterable $generators = [],
    ) {
        $this->generators = is_array($generators)
            ? $generators
            : iterator_to_array($generators, false);
    }

    /**
     * Registra un generador.
     */
    public function register(
        GeneratorInterface $generator,
    ): self {
        $this->generators[] = $generator;

        return $this;
    }

    /**
     * Ejecuta todos los generadores registrados.
     */
    public function generate(
        ModuleData $module,
    ): GeneratorResult {

        $result = new GeneratorResult();

        foreach ($this->generators as $generator) {

            $current = $this->executeGenerator(
                $generator,
                $module,
            );

            $result->merge($current);

            if ($current->hasErrors()) {
                break;
            }
        }

        return $result;
    }

    /**
     * Ejecuta un único generador.
     */
    private function executeGenerator(
        GeneratorInterface $generator,
        ModuleData $module,
    ): GeneratorResult {
        return $generator->generate($module);
    }

    public function registered(): array{
        return [];
    }
}
