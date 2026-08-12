<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use PHPUnit\Framework\TestCase;

final class AuditQueryInterfaceActionTest extends TestCase
{
    public function test_query_accepts_action(): void
    {
        $query = $this->createMock(
            AuditQueryInterface::class
        );

        $query
            ->expects($this->once())
            ->method('byAction')
            ->with('authorization.changed');

        $query->byAction(
            'authorization.changed'
        );
    }
}

