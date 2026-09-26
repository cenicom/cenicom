<?php

declare(strict_types=1);

namespace App\Core\Activity\Providers;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Core\Activity\Infrastructure\AuditRecentActivityReader;
use Illuminate\Support\ServiceProvider;

final class ActivityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RecentActivityReaderInterface::class,
            AuditRecentActivityReader::class,
        );
    }

    public function boot(): void
    {
    }
}
