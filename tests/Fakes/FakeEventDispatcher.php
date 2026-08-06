<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Core\Contracts\Events\EventDispatcherInterface;
use PHPUnit\Framework\Assert;

/**
 * Fake event dispatcher for unit and integration tests.
 */
final class FakeEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var list<object>
     */
    private array $events = [];

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event): void
    {
        $this->events[] = $event;
    }

    /**
     * Returns all dispatched events.
     *
     * @return list<object>
     */
    public function events(): array
    {
        return $this->events;
    }

    /**
     * Determines whether an event was dispatched.
     */
    public function hasDispatched(string $eventClass): bool
    {
        foreach ($this->events as $event) {
            if ($event instanceof $eventClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * Asserts that the given event was dispatched.
     */
    public function assertDispatched(string $eventClass): void
    {
        Assert::assertTrue(
            $this->hasDispatched($eventClass),
            sprintf(
                'Failed asserting that event [%s] was dispatched.',
                $eventClass
            )
        );
    }

    /**
     * Clears all dispatched events.
     */
    public function clear(): void
    {
        $this->events = [];
    }

    /**
     * Returns the number of dispatched events.
     */
    public function count(): int
    {
        return count($this->events);
    }
}
