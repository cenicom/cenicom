<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Activity;

use App\Core\Activity\DTO\ActivityReadModel;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ActivityReadModelTest extends TestCase
{
    public function test_stores_recent_activity_data_without_presentation_concerns(): void
    {
        $occurredAt = new DateTimeImmutable('2026-09-25 12:00:00');

        $activity = new ActivityReadModel(
            eventType: 'institution.created',
            occurredAt: $occurredAt,
            actorId: '1',
            actorName: 'Test User',
            actorAuthenticated: true,
            subjectType: 'institution',
            subjectId: '01JTESTINSTITUTION000000000001',
            data: [
                'name' => 'Escuela Batalla de Boyacá',
            ],
            result: 'success',
        );

        $this->assertSame(
            'institution.created',
            $activity->eventType,
        );

        $this->assertSame(
            $occurredAt,
            $activity->occurredAt,
        );

        $this->assertSame(
            '1',
            $activity->actorId,
        );

        $this->assertSame(
            'Test User',
            $activity->actorName,
        );

        $this->assertTrue(
            $activity->actorAuthenticated,
        );

        $this->assertSame(
            'institution',
            $activity->subjectType,
        );

        $this->assertSame(
            '01JTESTINSTITUTION000000000001',
            $activity->subjectId,
        );

        $this->assertSame(
            ['name' => 'Escuela Batalla de Boyacá'],
            $activity->data,
        );

        $this->assertSame(
            'success',
            $activity->result,
        );
    }
}
