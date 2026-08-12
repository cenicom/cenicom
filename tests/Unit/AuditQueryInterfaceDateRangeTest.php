<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AuditQueryInterfaceDateRangeTest extends TestCase
{
    public function test_query_accepts_date_range(): void
    {
        $query = $this->createMock(
            AuditQueryInterface::class
        );

        $from = new DateTimeImmutable(
            '2026-08-01 00:00:00'
        );

        $to = new DateTimeImmutable(
            '2026-08-11 23:59:59'
        );

        $query
            ->expects($this->once())
            ->method('between')
            ->with(
                $from,
                $to
            );

        $query->between(
            $from,
            $to
        );
    }
}
