<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditEntryData;
use App\Core\Audit\DTO\AuditSubject;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditAuthorizationChangedIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_user_authorization_change(): void
    {
        event(
            AuthorizationChanged::user(15)
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 15,
            'result' => 'success',
        ]);
    }

    public function test_records_role_authorization_change(): void
    {
        event(
            AuthorizationChanged::role('teacher')
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'role',
            'subject_id' => 'teacher',
            'result' => 'success',
        ]);
    }

    public function test_records_current_actor(): void
    {
        $identity = new \App\Core\Security\Identity\Identity(
            id: 15,
            name: 'Juan Pérez',
            roles: ['administrator'],
            permissions: ['authorization.manage'],
        );

        $this->app->instance(
            \App\Core\Security\Contracts\IdentityInterface::class,
            $identity,
        );

        event(
            \App\Core\Security\Authorization\Events\AuthorizationChanged::user(25)
        );

        $this->assertDatabaseHas('audit_entries', [
            'actor_id' => 15,
            'actor_name' => 'Juan Pérez',
            'actor_authenticated' => 1,
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => 25,
            'result' => 'success',
        ]);
    }

    public function test_authorization_change_can_be_read_through_audit_query(): void
    {
        $identity = new \App\Core\Security\Identity\Identity(
            id: 15,
            name: 'Juan Pérez',
            roles: ['administrator'],
            permissions: ['authorization.manage'],
        );

        $this->app->instance(
            \App\Core\Security\Contracts\IdentityInterface::class,
            $identity,
        );

        event(
            AuthorizationChanged::user(25)
        );

        $query = $this->app->make(
            AuditQueryInterface::class
        );

        $entries = iterator_to_array(
            $query->bySubject(
                new AuditSubject(
                    type: 'user',
                    id: 25,
                )
            )
        );

        $this->assertCount(1, $entries);

        $entry = $entries[0];

        $this->assertInstanceOf(
            AuditEntryData::class,
            $entry
        );

        $this->assertSame(
            '15',
            $entry->actorId
        );

        $this->assertSame(
            'Juan Pérez',
            $entry->actorName
        );

        $this->assertTrue(
            $entry->actorAuthenticated
        );

        $this->assertSame(
            'authorization.changed',
            $entry->action
        );

        $this->assertSame(
            'user',
            $entry->subjectType
        );

        $this->assertSame(
            '25',
            $entry->subjectId
        );

        $this->assertSame(
            ['scope' => 'user'],
            $entry->metadata
        );

        $this->assertSame(
            'success',
            $entry->result
        );
    }
}
