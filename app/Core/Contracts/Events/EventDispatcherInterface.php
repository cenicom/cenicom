<?php

declare(strict_types=1);

namespace App\Core\Contracts\Events;

/**
 * Defines the contract for dispatching domain events.
 *
 * Implementations are responsible for delivering events
 * to the underlying event system.
 */
interface EventDispatcherInterface
{
    /**
     * Dispatches the given event.
     */
    public function dispatch(object $event): void;
}
