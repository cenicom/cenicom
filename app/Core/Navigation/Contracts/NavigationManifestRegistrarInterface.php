<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationManifestData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato para registrar Navigation Manifests.
 *
 * Responsabilidades:
 *
 * - Registrar grupos.
 * - Registrar ítems.
 * - Poblar el NavigationRegistry mediante
 *   NavigationRegistrar.
 *
 * No debe:
 *
 * - Construir árboles.
 * - Resolver permisos.
 * - Leer archivos.
 *
 * ==========================================================
 */
interface NavigationManifestRegistrarInterface
{

    public function register(NavigationManifestData $manifests): void;
}
