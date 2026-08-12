<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Core\Audit\Persistence\EloquentAuditRepository;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EloquentAuditRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_stores_audit_entry(): void
    {
        $entry = new AuditEntry(
            actor: new AuditActor(
                id: 15,
                name: 'Juan Pérez',
                authenticated: true,
            ),
            action: 'authorization.changed',
            subject: new AuditSubject(
                type: 'user',
                id: 25,
            ),
            metadata: [
                'permission' => 'navigation.view',
            ],
            result: 'success',
            occurredAt: new \DateTimeImmutable(
                '2026-08-11 16:00:00'
            ),
        );

        $repository = new EloquentAuditRepository();

        $repository->store($entry);

        $this->assertDatabaseHas('audit_entries', [
            'actor_id' => 15,
            'actor_name' => 'Juan Pérez',
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => '25',
            'result' => 'success',
        ]);
    }

    /**
     * Summary of test_preserves_subject_metadata_and_occurred_at
     * persiste correctamente subject, metadata y occurredAt.
     * @return void
     */
    public function test_preserves_subject_metadata_and_occurred_at(): void
    {
        $entry = new AuditEntry(
            actor: new AuditActor(
                id: 'user-123',
                name: 'Juan Pérez',
                authenticated: true,
            ),
            action: 'authorization.changed',
            subject: new AuditSubject(
                type: 'role',
                id: 'teacher',
            ),
            metadata: [
                'permission' => 'navigation.view',
                'scope' => 'role',
            ],
            result: 'success',
            occurredAt: new \DateTimeImmutable(
                '2026-08-11 16:00:00'
            ),
        );

        $repository = new EloquentAuditRepository();

        $repository->store($entry);

        $this->assertDatabaseHas('audit_entries', [
            'actor_id' => 'user-123',
            'subject_type' => 'role',
            'subject_id' => 'teacher',
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:00:00',
        ]);

        $audit = AuditLog::query()->first();

        $this->assertNotNull($audit);

        $this->assertSame([
            'permission' => 'navigation.view',
            'scope' => 'role',
        ], $audit->metadata);
    }

    /* ⚓ Correcto. Vamos a cerrar esa frontera con un único caso explícito. */
    public function test_stores_entry_without_subject(): void
    {
        $entry = new AuditEntry(
            actor: new AuditActor(
                id: 15,
                name: 'Juan Pérez',
                authenticated: true,
            ),
            action: 'session.login',
            subject: null,
            metadata: [],
            result: 'success',
            occurredAt: new \DateTimeImmutable(
                '2026-08-11 16:00:00'
            ),
        );

        $repository = new EloquentAuditRepository();

        $repository->store($entry);

        $this->assertDatabaseHas('audit_entries', [
            'actor_id' => 15,
            'action' => 'session.login',
            'subject_type' => null,
            'subject_id' => null,
            'result' => 'success',
        ]);
    }
}
