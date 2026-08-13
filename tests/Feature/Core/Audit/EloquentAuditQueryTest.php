<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditSubject;
use App\Core\Audit\Persistence\EloquentAuditQuery;
use App\Models\AuditLog;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EloquentAuditQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_finds_entries_by_subject(): void
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

        AuditLog::query()->create([
            'actor_id' => 15,
            'actor_name' => 'Juan Pérez',
            'actor_authenticated' => true,
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 20,
            'metadata' => [
                'scope' => 'user',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:05:00',
        ]);

        AuditLog::query()->create([
            'actor_id' => 15,
            'actor_name' => 'Juan Pérez',
            'actor_authenticated' => true,
            'action' => 'authorization.changed',
            'subject_type' => 'role',
            'subject_id' => 15,
            'metadata' => [
                'scope' => 'role',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:10:00',
        ]);

        $subject = new AuditSubject(
            type: 'user',
            id: 15,
        );

        $query = new EloquentAuditQuery();

        $entries = iterator_to_array(
            $query->bySubject($subject)
        );

        $this->assertCount(1, $entries);

        $entry = $entries[0];

        $this->assertSame(
            'user',
            $entry->subjectType
        );

        $this->assertSame(
            15,
            (int) $entry->subjectId
        );
    }

    /* ⚓ Ahora vamos con byAction(), manteniendo el mismo enfoque:
     * primero una prueba concreta de persistencia, sin tocar todavía
     * actor ni fechas. */
    public function test_finds_entries_by_action(): void
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

        AuditLog::query()->create([
            'actor_id' => 15,
            'actor_name' => 'Juan Pérez',
            'actor_authenticated' => true,
            'action' => 'login',
            'subject_type' => 'user',
            'subject_id' => 15,
            'metadata' => [
                'scope' => 'authentication',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:05:00',
        ]);

        AuditLog::query()->create([
            'actor_id' => 20,
            'actor_name' => 'María López',
            'actor_authenticated' => true,
            'action' => 'authorization.changed',
            'subject_type' => 'role',
            'subject_id' => 5,
            'metadata' => [
                'scope' => 'role',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:10:00',
        ]);

        $query = new EloquentAuditQuery();

        $entries = iterator_to_array(
            $query->byAction('authorization.changed')
        );

        $this->assertCount(2, $entries);

        $this->assertSame(
            'authorization.changed',
            $entries[0]->action
        );

        $this->assertSame(
            'authorization.changed',
            $entries[1]->action
        );
    }

    /* Actor autenticado → id, name, authenticated=true
     * Sistema/Guest → id=null, name='Guest' o 'System',
     * authenticated=false
     * Primero probemos el caso normal: actor autenticado. */
    public function test_finds_entries_by_actor(): void
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

        AuditLog::query()->create([
            'actor_id' => 20,
            'actor_name' => 'María López',
            'actor_authenticated' => true,
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 20,
            'metadata' => [
                'scope' => 'user',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:05:00',
        ]);

        $actor = new AuditActor(
            id: 15,
            name: 'Juan Pérez',
            authenticated: true,
        );

        $query = new EloquentAuditQuery();

        $entries = iterator_to_array(
            $query->byActor($actor)
        );

        $this->assertCount(1, $entries);

        $this->assertSame(
            15,
            (int) $entries[0]->actorId
        );

        $this->assertSame(
            'Juan Pérez',
            $entries[0]->actorName
        );
    }

    /*
     * Guest/System
     * Aquí queremos garantizar que una consulta por actor con:
     * id = null
     * name = Guest
     * authenticated = false */
    public function test_finds_entries_by_guest_actor(): void
    {
        AuditLog::query()->create([
            'actor_id' => null,
            'actor_name' => 'Guest',
            'actor_authenticated' => false,
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 15,
            'metadata' => [
                'scope' => 'user',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:00:00',
        ]);

        AuditLog::query()->create([
            'actor_id' => null,
            'actor_name' => 'System',
            'actor_authenticated' => false,
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 20,
            'metadata' => [
                'scope' => 'user',
            ],
            'result' => 'success',
            'occurred_at' => '2026-08-11 16:05:00',
        ]);

        $actor = new AuditActor(
            id: null,
            name: 'Guest',
            authenticated: false,
        );

        $query = new EloquentAuditQuery();

        $entries = iterator_to_array(
            $query->byActor($actor)
        );

        $this->assertCount(1, $entries);

        $this->assertNull(
            $entries[0]->actorId
        );

        $this->assertSame(
            'Guest',
            $entries[0]->actorName
        );

        $this->assertFalse(
            (bool) $entries[0]->actorAuthenticated
        );
    }

    /*
     * Undocumented function
     * between()
     * @return void
     */
    public function test_finds_entries_between_dates(): void
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
            'occurred_at' => '2026-08-11 10:00:00',
        ]);

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
            'occurred_at' => '2026-08-11 12:00:00',
        ]);

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
            'occurred_at' => '2026-08-11 14:00:00',
        ]);

        $from = new DateTimeImmutable(
            '2026-08-11 11:00:00'
        );

        $to = new DateTimeImmutable(
            '2026-08-11 13:00:00'
        );

        $query = new EloquentAuditQuery();

        $entries = iterator_to_array(
            $query->between($from, $to)
        );

        $this->assertCount(1, $entries);

        $this->assertSame(
            '2026-08-11 12:00:00',
            $entries[0]->occurredAt->format('Y-m-d H:i:s')
        );
    }
}
