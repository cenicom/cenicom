<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\AuditRecorder;
use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use PHPUnit\Framework\TestCase;

final class AuditRecorderTest extends TestCase
{
    public function test_delegates_audit_entry_to_repository(): void
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

        $repository = $this->createMock(
            AuditRepositoryInterface::class
        );

        $repository
            ->expects($this->once())
            ->method('store')
            ->with($this->identicalTo($entry));

        $recorder = new AuditRecorder(
            $repository
        );

        $recorder->record($entry);
    }
}
