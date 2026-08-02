<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Descubre los Navigation Manifest disponibles.
 *
 * Responsabilidades:
 *
 * - Localizar manifests de navegación.
 * - Devolver DTOs de NavigationManifestData.
 *
 * No debe:
 *
 * - Leer el contenido del manifest.
 * - Registrar navegación.
 * - Construir árboles.
 *
 * ==========================================================
 */
interface NavigationManifestFinderInterface
{

    public function discover(): array;
}
