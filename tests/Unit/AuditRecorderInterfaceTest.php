<?php

declare(strict_types=1);

namespace Tests\Unit;


use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use PHPUnit\Framework\TestCase;

final class AuditRecorderInterfaceTest extends TestCase
{
    public function test_recorder_accepts_audit_entry(): void
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
            metadata: [],
            result: 'success',
            occurredAt: new \DateTimeImmutable(
                '2026-08-11 16:00:00'
            ),
        );

        $recorded = null;

        $recorder = new class ($recorded)
            implements AuditRecorderInterface
        {
            public function __construct(
                private mixed &$recorded,
            ) {
            }

            public function record(
                AuditEntry $entry
            ): void {
                $this->recorded = $entry;
            }
        };

        $recorder->record($entry);

        $this->assertSame($entry, $recorded);
    }
}
