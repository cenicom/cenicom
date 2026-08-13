<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditEntryData;
use App\Core\Audit\DTO\AuditSubject;
use App\Models\AuditLog;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditWriteReadConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_recorded_entry_can_be_read_through_audit_query_contract(): void
    {
        $occurredAt = new DateTimeImmutable(
            '2026-08-11 18:00:00'
        );

        $entry = new AuditEntry(
            actor: new AuditActor(
                id: '15',
                name: 'Juan Pérez',
                authenticated: true,
            ),
            action: 'authorization.changed',
            subject: new AuditSubject(
                type: 'user',
                id: 15,
            ),
            metadata: [
                'scope' => 'user',
                'source' => 'integration-test',
            ],
            result: 'success',
            occurredAt: $occurredAt,
        );

        $recorder = $this->app->make(
            AuditRecorderInterface::class
        );

        $recorder->record($entry);

        $query = $this->app->make(
            AuditQueryInterface::class
        );

        $entries = iterator_to_array(
            $query->bySubject(
                new AuditSubject(
                    type: 'user',
                    id: 15,
                )
            )
        );

        self::assertCount(1, $entries);

        $result = $entries[0];

        self::assertInstanceOf(
            AuditEntryData::class,
            $result
        );

        self::assertNotInstanceOf(
            AuditLog::class,
            $result
        );

        self::assertSame(
            '15',
            $result->actorId
        );

        self::assertSame(
            'Juan Pérez',
            $result->actorName
        );

        self::assertTrue(
            $result->actorAuthenticated
        );

        self::assertSame(
            'authorization.changed',
            $result->action
        );

        self::assertSame(
            'user',
            $result->subjectType
        );

        self::assertSame(
            '15',
            $result->subjectId
        );

        self::assertSame(
            [
                'scope' => 'user',
                'source' => 'integration-test',
            ],
            $result->metadata
        );

        self::assertSame(
            'success',
            $result->result
        );

        self::assertSame(
            $occurredAt->format('Y-m-d H:i:s'),
            $result->occurredAt->format('Y-m-d H:i:s')
        );
    }
}
