<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntryData;
use App\Core\Audit\DTO\AuditSubject;
use App\Models\AuditLog;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditQueryContractIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_by_subject_returns_audit_entry_data(): void
    {
        AuditLog::query()->create([
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

        $entries = iterator_to_array(
            $query->bySubject(
                new AuditSubject(
                    type: 'user',
                    id: 15,
                )
            )
        );

        self::assertCount(1, $entries);
        self::assertInstanceOf(
            AuditEntryData::class,
            $entries[0]
        );
    }

    public function test_by_action_returns_audit_entry_data(): void
    {
        AuditLog::query()->create([
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

        $entries = iterator_to_array(
            $query->byAction('authorization.changed')
        );

        self::assertCount(1, $entries);
        self::assertInstanceOf(
            AuditEntryData::class,
            $entries[0]
        );
    }

    public function test_by_actor_returns_audit_entry_data(): void
    {
        AuditLog::query()->create([
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

        $entries = iterator_to_array(
            $query->byActor(
                new AuditActor(
                    id: '15',
                    name: 'Juan Pérez',
                    authenticated: true,
                )
            )
        );

        self::assertCount(1, $entries);
        self::assertInstanceOf(
            AuditEntryData::class,
            $entries[0]
        );
    }

    public function test_between_returns_audit_entry_data(): void
    {
        AuditLog::query()->create([
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

        $entries = iterator_to_array(
            $query->between(
                new DateTimeImmutable('2026-08-11 15:00:00'),
                new DateTimeImmutable('2026-08-11 17:00:00'),
            )
        );

        self::assertCount(1, $entries);
        self::assertInstanceOf(
            AuditEntryData::class,
            $entries[0]
        );
    }

    public function test_find_returns_audit_entry_data(): void
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

        $entry = $query->find($audit->id);

        self::assertInstanceOf(
            AuditEntryData::class,
            $entry
        );

        self::assertNotInstanceOf(
            AuditLog::class,
            $entry
        );
    }

    public function test_find_returns_null_when_entry_does_not_exist(): void
    {
        $query = $this->app->make(AuditQueryInterface::class);

        self::assertNull(
            $query->find(999999)
        );
    }
}
