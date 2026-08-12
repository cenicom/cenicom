<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditActor;
use PHPUnit\Framework\TestCase;

final class AuditQueryInterfaceActorTest extends TestCase
{
    public function test_query_accepts_actor(): void
    {
        $query = $this->createMock(
            AuditQueryInterface::class
        );

        $actor = new AuditActor(
            id: 15,
            name: 'Juan Pérez',
            authenticated: true,
        );

        $query
            ->expects($this->once())
            ->method('byActor')
            ->with($actor);

        $query->byActor($actor);
    }
}
