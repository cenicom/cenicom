<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Activity\Infrastructure;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Core\Activity\Infrastructure\AuditRecentActivityReader;
use Tests\TestCase;

final class RecentActivityReaderContainerTest extends TestCase
{
    public function test_recent_activity_reader_is_resolved_from_container(): void
    {
        $reader = $this->app->make(
            RecentActivityReaderInterface::class,
        );

        $this->assertInstanceOf(
            AuditRecentActivityReader::class,
            $reader,
        );
    }
}
