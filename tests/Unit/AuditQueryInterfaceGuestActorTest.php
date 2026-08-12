<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditActor;
use PHPUnit\Framework\TestCase;

final class AuditQueryInterfaceGuestActorTest extends TestCase
{
    public function test_query_accepts_guest_actor(): void
    {
        $query = $this->createMock(
            AuditQueryInterface::class
        );

        $actor = new AuditActor(
            id: null,
            name: 'Guest',
            authenticated: false,
        );

        $query
            ->expects($this->once())
            ->method('byActor')
            ->with($actor);

        $query->byActor($actor);
    }
}
