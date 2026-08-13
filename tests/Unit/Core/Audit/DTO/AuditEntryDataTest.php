<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Audit\DTO;

use App\Core\Audit\DTO\AuditEntryData;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AuditEntryDataTest extends TestCase
{
    public function test_it_creates_an_audit_entry_data(): void
    {
        $occurredAt = new DateTimeImmutable('2026-08-12 10:30:00');
        $createdAt = new DateTimeImmutable('2026-08-12 10:30:01');
        $updatedAt = new DateTimeImmutable('2026-08-12 10:30:02');

        $data = new AuditEntryData(
            actorId: '15',
            actorName: 'Luis',
            actorAuthenticated: true,
            action: 'created',
            subjectType: 'App\\Models\\User',
            subjectId: '42',
            metadata: [
                'source' => 'web',
                'module' => 'users',
            ],
            result: 'success',
            occurredAt: $occurredAt,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        self::assertSame('15', $data->actorId);
        self::assertSame('Luis', $data->actorName);
        self::assertTrue($data->actorAuthenticated);
        self::assertSame('created', $data->action);
        self::assertSame('App\\Models\\User', $data->subjectType);
        self::assertSame('42', $data->subjectId);
        self::assertSame(
            [
                'source' => 'web',
                'module' => 'users',
            ],
            $data->metadata
        );
        self::assertSame('success', $data->result);
        self::assertSame($occurredAt, $data->occurredAt);
        self::assertSame($createdAt, $data->createdAt);
        self::assertSame($updatedAt, $data->updatedAt);
    }

    public function test_it_accepts_nullable_actor_name(): void
    {
        $data = new AuditEntryData(
            actorId: '15',
            actorName: null,
            actorAuthenticated: false,
            action: 'system_event',
            subjectType: null,
            subjectId: null,
            metadata: [],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-08-12 10:30:00'),
            createdAt: new DateTimeImmutable('2026-08-12 10:30:01'),
            updatedAt: new DateTimeImmutable('2026-08-12 10:30:02'),
        );

        self::assertNull($data->actorName);
        self::assertFalse($data->actorAuthenticated);
        self::assertNull($data->subjectType);
        self::assertNull($data->subjectId);
        self::assertSame([], $data->metadata);
    }

    public function test_it_is_immutable(): void
    {
        $data = new AuditEntryData(
            actorId: '15',
            actorName: 'Luis',
            actorAuthenticated: true,
            action: 'created',
            subjectType: 'App\\Models\\User',
            subjectId: '42',
            metadata: [],
            result: 'success',
            occurredAt: new DateTimeImmutable('2026-08-12 10:30:00'),
            createdAt: new DateTimeImmutable('2026-08-12 10:30:01'),
            updatedAt: new DateTimeImmutable('2026-08-12 10:30:02'),
        );

        $reflection = new \ReflectionClass($data);

        self::assertTrue($reflection->isFinal());
        self::assertTrue($reflection->isReadOnly());
    }
}
