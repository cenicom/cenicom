<?php

declare(strict_types=1);

namespace App\Core\Module\Lifecycle;

/**
 * Represents the lifecycle state of a module.
 *
 * This enum defines the canonical states that a module can traverse
 * from discovery to shutdown or failure.
 */
enum ModuleState: string
{
    /**
     * The module has been discovered by the discovery process.
     */
    case DISCOVERED = 'discovered';

    /**
     * The module has been registered in the module registry.
     */
    case REGISTERED = 'registered';

    /**
     * The module is executing its bootstrap process.
     */
    case BOOTING = 'booting';

    /**
     * The bootstrap process completed successfully.
     */
    case BOOTED = 'booted';

    /**
     * The module is fully operational.
     */
    case RUNNING = 'running';

    /**
     * The module has been stopped gracefully.
     */
    case STOPPED = 'stopped';

    /**
     * The module entered a failure state.
     */
    case FAILED = 'failed';
}
