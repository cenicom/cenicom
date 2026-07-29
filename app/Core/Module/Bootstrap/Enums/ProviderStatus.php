<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Enums;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa el estado del ciclo de vida de un
 * Service Provider durante el Bootstrapping.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
enum ProviderStatus: string
{
    /**
     * El Provider fue descubierto.
     */
    case DISCOVERED = 'discovered';

    /**
     * El Provider superó todas las validaciones.
     */
    case VALIDATED = 'validated';

    /**
     * El Provider fue registrado correctamente.
     */
    case REGISTERED = 'registered';

    /**
     * El Provider produjo un error.
     */
    case FAILED = 'failed';

    /**
     * El Provider fue omitido.
     */
    case SKIPPED = 'skipped';
}
