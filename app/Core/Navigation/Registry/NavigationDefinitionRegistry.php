<?php

declare(strict_types=1);

namespace App\Core\Navigation\Registry;

final class NavigationDefinitionRegistry
{
    /**
     * @var array<int, class-string>
     */
    private array $definitions = [];


    /**
     * Agrega una definición de navegación.
     */
    public function add(
        string $definition
    ): void {

        $this->definitions[] = $definition;
    }


    /**
     * Obtiene las definiciones registradas.
     *
     * @return array<int, class-string>
     */
    public function definitions(): array
    {
        return $this->definitions;
    }


    /**
     * Limpia las definiciones.
     *
     * Útil para pruebas.
     */
    public function clear(): void
    {
        $this->definitions = [];
    }
}
