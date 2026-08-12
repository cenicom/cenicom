<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use PHPUnit\Framework\TestCase;

final class AuditEntryTest extends TestCase
{
    public function test_creates_audit_entry_with_required_data(): void
    {
        $actor = new AuditActor(
            id: 15,
            name: 'Juan Pérez',
            authenticated: true,
        );

        $subject = new AuditSubject(
            type: 'user',
            id: 25,
        );

        $occurredAt = new \DateTimeImmutable(
            '2026-08-11 16:00:00'
        );

        $entry = new AuditEntry(
            actor: $actor,
            action: 'authorization.changed',
            subject: $subject,
            metadata: [
                'scope' => 'user',
            ],
            result: 'success',
            occurredAt: $occurredAt,
        );

        $this->assertSame($actor, $entry->actor);
        $this->assertSame(
            'authorization.changed',
            $entry->action
        );
        $this->assertSame($subject, $entry->subject);
        $this->assertSame(
            ['scope' => 'user'],
            $entry->metadata
        );
        $this->assertSame('success', $entry->result);
        $this->assertSame($occurredAt, $entry->occurredAt);
    }

    public function test_allows_entry_without_subject(): void
    {
        $actor = new AuditActor(
            id: 15,
            name: 'Juan Pérez',
            authenticated: true,
        );

        $occurredAt = new \DateTimeImmutable();

        $entry = new AuditEntry(
            actor: $actor,
            action: 'session.login',
            subject: null,
            metadata: [],
            result: 'success',
            occurredAt: $occurredAt,
        );

        $this->assertSame($actor, $entry->actor);
        $this->assertNull($entry->subject);
        $this->assertSame('session.login', $entry->action);
    }

    public function test_preserves_metadata(): void
    {
        $metadata = [
            'scope' => 'role',
            'role' => 'teacher',
            'permission' => 'navigation.view',
        ];

        $entry = new AuditEntry(
            actor: new AuditActor(
                id: 15,
                name: 'Juan Pérez',
                authenticated: true,
            ),
            action: 'authorization.changed',
            subject: null,
            metadata: $metadata,
            result: 'success',
            occurredAt: new \DateTimeImmutable(),
        );

        $this->assertSame($metadata, $entry->metadata);
    }

    public function test_preserves_result(): void
    {
        $entry = new AuditEntry(
            actor: new AuditActor(
                id: 15,
                name: 'Juan Pérez',
                authenticated: true,
            ),
            action: 'authorization.changed',
            subject: null,
            metadata: [],
            result: 'failed',
            occurredAt: new \DateTimeImmutable(),
        );

        $this->assertSame('failed', $entry->result);
    }
}
