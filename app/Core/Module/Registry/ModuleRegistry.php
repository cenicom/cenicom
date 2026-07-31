<?php

declare(strict_types=1);

namespace App\Core\Module\Registry;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\DTO\ModuleDefinition;




final class ModuleRegistry implements ModuleRegistryInterface
{
    /**
     * Colección de módulos registrados.
     *
     * @var array<string, ModuleDefinition>
     */
    private array $modules = [];

    /**
     * Registra un módulo.
     */
    public function register(ModuleDefinition $module): void
    {
        $this->modules[$module->name] = $module;
    }

    /**
     * Determina si un módulo existe.
     */
    public function has(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    /**
     * Obtiene un módulo por nombre.
     */
    public function get(string $name): ?ModuleDefinition
    {
        return $this->modules[$name] ?? null;
    }

    /**
     * Obtiene todos los módulos registrados.
     *
     * @return array<int, ModuleDefinition>
     */
    public function all(): array
    {
        return array_values($this->modules);
    }

    /**
     * Elimina un módulo.
     */
    public function remove(string $name): void
    {
        unset($this->modules[$name]);
    }

    /**
     * Vacía completamente el registro.
     */
    public function clear(): void
    {
        $this->modules = [];
    }

    /**
     * Obtiene la cantidad de módulos registrados.
     */
    public function count(): int
    {
        return count($this->modules);
    }

    /**
     * Obtiene los nombres de todos los módulos registrados.
     *
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_map(
            static fn(ModuleDefinition $module): string => $module->name,
            $this->all(),
        );
    }

    private function createDefinition(string $name): ModuleDefinition
    {
        return new ModuleDefinition(
            name: $name,
            namespace: "Modules\\{$name}",
            basePath: "/modules/{$name}",
            manifestPath: "/modules/{$name}/module.php",
            providers: [],
            enabled: true,
        );

        $definition = $this->createDefinition('Blog');
    }
}
