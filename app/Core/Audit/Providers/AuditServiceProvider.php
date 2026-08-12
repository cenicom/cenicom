<?php

declare(strict_types=1);

namespace App\Core\Audit\Providers;

use App\Core\Audit\AuditRecorder;
use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\Listeners\AuditAuthorizationChangedListener;
use App\Core\Audit\Persistence\EloquentAuditQuery;
use App\Core\Audit\Persistence\EloquentAuditRepository;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

final class AuditServiceProvider extends ServiceProvider
{
    /**
     * /🟢 Siguiente maniobra: implementar register()
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            AuditRepositoryInterface::class,
            EloquentAuditRepository::class,
        );

        $this->app->bind(
            AuditRecorderInterface::class,
            AuditRecorder::class,
        );

        $this->app->bind(
            AuditQueryInterface::class,
            EloquentAuditQuery::class
        );
    }

    public function boot(): void
    {
        $this->app
            ->make(Dispatcher::class)
            ->listen(
                AuthorizationChanged::class,
                AuditAuthorizationChangedListener::class,
            );
    }


}
