<?php

declare(strict_types=1);

namespace App\Core\Module\Events;

use DateTimeImmutable;

/**
 * Base class for all module lifecycle events.
 *
 * Provides the common information shared by every
 * event emitted by the module subsystem.
 */
abstract class AbstractModuleEvent
{
    /**
     * Creates a new module event.
     */
    public function __construct(
        private readonly string $module,
        private readonly DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    /**
     * Returns the module name.
     */
    public function module(): string
    {
        return $this->module;
    }

    /**
     * Returns the event occurrence timestamp.
     */
    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /**
     * Returns the event payload as an array.
     *
     * Useful for logging, telemetry and diagnostics.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'module' => $this->module,
            'occurred_at' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
}
