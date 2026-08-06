<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Events;

use App\Core\Module\Events\AbstractModuleEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AbstractModuleEventTest extends TestCase
{
    public function test_it_stores_module_name(): void
    {
        $event = new TestModuleEvent('Inventory');

        $this->assertSame(
            'Inventory',
            $event->module()
        );
    }

    public function test_it_stores_occurrence_timestamp(): void
    {
        $event = new TestModuleEvent('Inventory');

        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $event->occurredAt()
        );
    }

    public function test_it_converts_to_array(): void
    {
        $occurredAt = new DateTimeImmutable('2026-01-15T10:30:45+00:00');

        $event = new TestModuleEvent(
            'Inventory',
            $occurredAt
        );

        $this->assertSame(
            [
                'module' => 'Inventory',
                'occurred_at' => $occurredAt->format(DATE_ATOM),
            ],
            $event->toArray()
        );
    }

    public function test_timestamp_is_serialized_using_date_atom(): void
    {
        $event = new TestModuleEvent('Inventory');

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T.*$/',
            $event->toArray()['occurred_at']
        );
    }
}

/**
 * Test implementation of AbstractModuleEvent.
 */
final class TestModuleEvent extends AbstractModuleEvent
{
}
