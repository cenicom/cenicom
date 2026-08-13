<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Audit\Contracts;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntryData;
use App\Core\Audit\DTO\AuditSubject;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AuditQueryInterfaceTest extends TestCase
{
    public function test_implementation_returns_audit_entry_data_from_find(): void
    {
        $query = new class implements AuditQueryInterface {
            public function bySubject(AuditSubject $subject): iterable
            {
                return [];
            }

            public function byAction(string $action): iterable
            {
                return [];
            }

            public function byActor(AuditActor $actor): iterable
            {
                return [];
            }

            public function between(
                DateTimeImmutable $from,
                DateTimeImmutable $to
            ): iterable {
                return [];
            }

            public function find(int $id): ?AuditEntryData
            {
                return new AuditEntryData(
                    actorId: '1',
                    actorName: 'Test User',
                    actorAuthenticated: true,
                    action: 'created',
                    subjectType: 'TestSubject',
                    subjectId: '1',
                    metadata: [],
                    result: 'success',
                    occurredAt: new DateTimeImmutable('2026-08-12 10:00:00'),
                    createdAt: new DateTimeImmutable('2026-08-12 10:00:01'),
                    updatedAt: new DateTimeImmutable('2026-08-12 10:00:02'),
                );
            }
        };

        $result = $query->find(1);

        self::assertInstanceOf(
            AuditEntryData::class,
            $result
        );
    }

    public function test_find_can_return_null(): void
    {
        $query = new class implements AuditQueryInterface {
            public function bySubject(AuditSubject $subject): iterable
            {
                return [];
            }

            public function byAction(string $action): iterable
            {
                return [];
            }

            public function byActor(AuditActor $actor): iterable
            {
                return [];
            }

            public function between(
                DateTimeImmutable $from,
                DateTimeImmutable $to
            ): iterable {
                return [];
            }

            public function find(int $id): ?AuditEntryData
            {
                return null;
            }
        };

        self::assertNull(
            $query->find(999)
        );
    }
}
