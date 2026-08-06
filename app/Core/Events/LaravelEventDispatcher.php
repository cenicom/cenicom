<?php

declare(strict_types=1);

namespace App\Core\Events;

use App\Core\Contracts\Events\EventDispatcherInterface;

final class LaravelEventDispatcher implements EventDispatcherInterface
{
    /**
     * Dispatches the given domain event using Laravel's event dispatcher.
     */
    public function dispatch(object $event): void
    {
        event($event);
    }
}
