<?php

declare(strict_types=1);

namespace Tests\Unit;


use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditSubject;
use PHPUnit\Framework\TestCase;

final class AuditQueryInterfaceTest extends TestCase
{
    public function test_query_accepts_subject(): void
    {
        $query = $this->createMock(
            AuditQueryInterface::class
        );

        $subject = new AuditSubject(
            type: 'user',
            id: 15,
        );

        $query
            ->expects($this->once())
            ->method('bySubject')
            ->with($subject);

        $query->bySubject($subject);
    }
}
