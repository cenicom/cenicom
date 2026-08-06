<?php

declare(strict_types=1);

namespace App\Core\Module\Manifest;

/**
 * DTO que representa el manifiesto de un módulo del CN Framework.
 *
 * El manifiesto describe toda la información necesaria para que un
 * módulo pueda ser descubierto, validado, registrado y cargado
 * automáticamente por el Module Bootstrap.
 *
 * Esta clase es completamente inmutable.
 */
final readonly class ModuleManifest
{
    /**
     * @param array<int,string> $providers
     * @param array<int,string> $dependencies
     * @param array<int,string> $permissions
     * @param array<string,mixed> $navigation
     */
    public function __construct(
        private string $name,
        private string $slug,
        private string $description,
        private string $version,
        private array $providers = [],
        private array $dependencies = [],
        private array $permissions = [],
        private array $navigation = [],
    ) {}

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Nombre del módulo.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Identificador único del módulo.
     */
    public function slug(): string
    {
        return $this->slug;
    }

    /**
     * Descripción del módulo.
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * Versión del módulo.
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * Lista de Service Providers.
     *
     * @return array<int,string>
     */
    public function providers(): array
    {
        return $this->providers;
    }

    /**
     * Dependencias requeridas.
     *
     * @return array<int,string>
     */
    public function dependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Permisos declarados por el módulo.
     *
     * @return array<int,string>
     */
    public function permissions(): array
    {
        return $this->permissions;
    }

    /**
     * Configuración de navegación.
     *
     * @return array<string,mixed>
     */
    public function navigation(): array
    {
        return $this->navigation;
    }

    /**
     * Convierte el manifiesto en un arreglo.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'version' => $this->version,
            'providers' => $this->providers,
            'dependencies' => $this->dependencies,
            'permissions' => $this->permissions,
            'navigation' => $this->navigation,
        ];
    }
}
