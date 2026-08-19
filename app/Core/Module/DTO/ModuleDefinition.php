<?php

declare(strict_types=1);

namespace App\Core\Module\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * DTO que representa un módulo descubierto por
 * la infraestructura modular durante el proceso
 * de Bootstrapping.
 *
 * ERP-INT-004
 *
 * @author CENICOM
 */
final readonly class ModuleDefinition
{
    /**
 * @param array<class-string> $providers
 * @param array<class-string> $permissionDefinitions
 * @param array<class-string> $navigationDefinitions
 * @param array<class-string> $crudDefinitions
 */
    public function __construct(
        public string $name,
        public string $namespace,
        public string $basePath,
        public string $manifestPath,
        public array $providers,
        public array $permissionDefinitions,
        public array $navigationDefinitions,
        public readonly bool $enabled,
        public array $crudDefinitions,
    ) {}
}
