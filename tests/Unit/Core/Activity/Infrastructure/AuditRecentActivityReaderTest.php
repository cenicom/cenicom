<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Activity\Infrastructure;

use App\Core\Activity\DTO\ActivityReadModel;
use App\Core\Activity\Infrastructure\AuditRecentActivityReader;
use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditEntryData;
use DateTimeImmutable;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

final class AuditRecentActivityReaderTest extends TestCase
{
    private MockInterface&AuditQueryInterface $auditQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditQuery = Mockery::mock(
            AuditQueryInterface::class,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_recent_returns_business_activities_ordered_by_occurrence_descending(): void
    {
        $older = new AuditEntryData(
            actorId: '1',
            actorName: 'User One',
            actorAuthenticated: true,
            action: 'institution.created',
            subjectType: 'institution',
            subjectId: 'INST-001',
            metadata: [
                'name' => 'Institution One',
            ],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-09-25 08:00:00'),
            createdAt: new DateTimeImmutable('2026-09-25 08:00:01'),
            updatedAt: new DateTimeImmutable('2026-09-25 08:00:01'),
        );

        $newer = new AuditEntryData(
            actorId: '2',
            actorName: 'User Two',
            actorAuthenticated: true,
            action: 'institution.updated',
            subjectType: 'institution',
            subjectId: 'INST-001',
            metadata: [
                'name' => 'Institution Updated',
            ],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-09-25 10:00:00'),
            createdAt: new DateTimeImmutable('2026-09-25 10:00:01'),
            updatedAt: new DateTimeImmutable('2026-09-25 10:00:01'),
        );

        $middle = new AuditEntryData(
            actorId: '3',
            actorName: 'User Three',
            actorAuthenticated: true,
            action: 'course.created',
            subjectType: 'course',
            subjectId: 'COURSE-001',
            metadata: [
                'name' => 'Mathematics',
            ],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-09-25 09:00:00'),
            createdAt: new DateTimeImmutable('2026-09-25 09:00:01'),
            updatedAt: new DateTimeImmutable('2026-09-25 09:00:01'),
        );

        $this->auditQuery
            ->shouldReceive('byAction')
            ->andReturnUsing(
                static function (string $action) use (
                    $older,
                    $middle,
                    $newer,
                ): iterable {
                    return match ($action) {
                        'institution.created' => [$older],
                        'institution.updated' => [$newer],
                        'course.created' => [$middle],
                        default => [],
                    };
                }
            );

        $reader = new AuditRecentActivityReader(
            $this->auditQuery,
        );

        $result = iterator_to_array(
            $reader->recent(5),
        );

        $this->assertCount(3, $result);

        $this->assertContainsOnlyInstancesOf(
            ActivityReadModel::class,
            $result,
        );

        $this->assertSame(
            'institution.updated',
            $result[0]->eventType,
        );

        $this->assertSame(
            'course.created',
            $result[1]->eventType,
        );

        $this->assertSame(
            'institution.created',
            $result[2]->eventType,
        );

        $this->assertSame(
            'User Two',
            $result[0]->actorName,
        );

        $this->assertSame(
            ['name' => 'Institution Updated'],
            $result[0]->data,
        );
    }

    public function test_recent_respects_requested_limit(): void
    {
        $first = new AuditEntryData(
            actorId: '1',
            actorName: 'User One',
            actorAuthenticated: true,
            action: 'institution.created',
            subjectType: 'institution',
            subjectId: 'INST-001',
            metadata: [],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-09-25 10:00:00'),
            createdAt: new DateTimeImmutable('2026-09-25 10:00:00'),
            updatedAt: new DateTimeImmutable('2026-09-25 10:00:00'),
        );

        $second = new AuditEntryData(
            actorId: '2',
            actorName: 'User Two',
            actorAuthenticated: true,
            action: 'institution.updated',
            subjectType: 'institution',
            subjectId: 'INST-002',
            metadata: [],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-09-25 09:00:00'),
            createdAt: new DateTimeImmutable('2026-09-25 09:00:00'),
            updatedAt: new DateTimeImmutable('2026-09-25 09:00:00'),
        );

        $third = new AuditEntryData(
            actorId: '3',
            actorName: 'User Three',
            actorAuthenticated: true,
            action: 'course.created',
            subjectType: 'course',
            subjectId: 'COURSE-001',
            metadata: [],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-09-25 08:00:00'),
            createdAt: new DateTimeImmutable('2026-09-25 08:00:00'),
            updatedAt: new DateTimeImmutable('2026-09-25 08:00:00'),
        );

        $this->auditQuery
            ->shouldReceive('byAction')
            ->andReturnUsing(
                static function (string $action) use (
                    $first,
                    $second,
                    $third,
                ): iterable {
                    return match ($action) {
                        'institution.created' => [$first],
                        'institution.updated' => [$second],
                        'course.created' => [$third],
                        default => [],
                    };
                }
            );

        $reader = new AuditRecentActivityReader(
            $this->auditQuery,
        );

        $result = iterator_to_array(
            $reader->recent(2),
        );

        $this->assertCount(2, $result);

        $this->assertSame(
            'institution.created',
            $result[0]->eventType,
        );

        $this->assertSame(
            'institution.updated',
            $result[1]->eventType,
        );
    }

    public function test_recent_rejects_non_positive_limit(): void
    {
        $reader = new AuditRecentActivityReader(
            $this->auditQuery,
        );

        $this->expectException(\InvalidArgumentException::class);

        $reader->recent(0);
    }

    public function test_recent_does_not_query_non_business_actions(): void
    {
        $queriedActions = [];

        $this->auditQuery
            ->shouldReceive('byAction')
            ->andReturnUsing(
                static function (string $action) use (&$queriedActions): iterable {
                    $queriedActions[] = $action;

                    return [];
                }
            );

        $reader = new AuditRecentActivityReader(
            $this->auditQuery,
        );

        $reader->recent(5);

        $this->assertNotContains(
            'authorization.changed',
            $queriedActions,
        );

        $this->assertSame(
            [
                'enrollment.created',
                'payment.recorded',
                'course.created',
                'user.updated',
                'report.generated',
                'institution.created',
                'institution.updated',
            ],
            $queriedActions,
        );
    }
}
