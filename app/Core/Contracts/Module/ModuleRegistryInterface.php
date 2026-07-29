<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;


use App\Core\Module\DTO\ModuleDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del registro de módulos.
 *
 * Responsable de administrar la colección
 * oficial de módulos registrados.
 */
interface ModuleRegistryInterface
{
    /**
     * Registra un módulo.
     */
    public function register(ModuleDefinition $module): void;

    /**
     * Determina si un módulo existe.
     */
    public function has(string $name): bool;

    /**
     * Obtiene un módulo por nombre.
     */
    public function get(string $name): ?ModuleDefinition;

    /**
     * Obtiene todos los módulos registrados.
     *
     * @return array<int, ModuleDefinition>
     */
    public function all(): array;

    /**
     * Elimina un módulo.
     */
    public function remove(string $name): void;

    /**
     * Vacía completamente el registro.
     */
    public function clear(): void;

    /**
     * Obtiene la cantidad de módulos registrados.
     */
    public function count(): int;

    /**
     * Obtiene los nombres de los módulos registrados.
     *
     * @return array<int,string>
     */
    public function names(): array;
}
