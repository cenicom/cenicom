<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditEntryData;
use App\Core\Audit\Persistence\EloquentAuditQuery;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditQueryResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_query_contract_resolves_to_eloquent_implementation(): void
    {
        $query = $this->app->make(AuditQueryInterface::class);

        self::assertInstanceOf(
            EloquentAuditQuery::class,
            $query
        );
    }

    public function test_resolved_query_returns_audit_entry_data(): void
    {
        $audit = AuditLog::query()->create([
            'actor_id' => 15,
            'actor_name' => 'Juan Pérez',
            'actor_authenticated' => true,
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 15,
            'metadata' => [
                'scope' => 'user',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:00:00',
        ]);

        $query = $this->app->make(AuditQueryInterface::class);

        $result = $query->find($audit->id);

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
    }
}
