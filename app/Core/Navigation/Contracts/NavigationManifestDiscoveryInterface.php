<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del servicio de descubrimiento de Navigation
 * Manifest.
 *
 * Responsabilidades:
 *
 * - Coordinar el proceso completo de descubrimiento.
 *
 * No debe:
 *
 * - Descubrir manifests.
 * - Leer archivos.
 * - Registrar navegación.
 * - Construir árboles.
 *
 * ==========================================================
 */
interface NavigationManifestDiscoveryInterface
{
    /**
     * Ejecuta el proceso completo de descubrimiento.
     */
    public function discover(): void;
}
