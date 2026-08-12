<?php

declare(strict_types=1);

namespace Tests\Unit;


use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use PHPUnit\Framework\TestCase;

final class AuditRepositoryInterfaceTest extends TestCase
{
    public function test_repository_accepts_audit_entry(): void
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

        $stored = null;

        $repository = new class ($stored)
            implements AuditRepositoryInterface
        {
            public function __construct(
                private mixed &$stored,
            ) {
            }

            public function store(
                AuditEntry $entry
            ): void {
                $this->stored = $entry;
            }
        };

        $repository->store($entry);

        $this->assertSame($entry, $stored);
    }
}
