<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Audit\Providers;

use App\Core\Audit\Listeners\AuditAuthorizationChangedListener;
use App\Core\Audit\Providers\AuditServiceProvider;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\TestCase;

final class AuditServiceProviderTest extends TestCase
{
    public function test_registers_authorization_changed_listener(): void
    {
        $events = $this->createMock(
            Dispatcher::class
        );

        $events
            ->expects($this->once())
            ->method('listen')
            ->with(
                AuthorizationChanged::class,
                AuditAuthorizationChangedListener::class,
            );

        $app = $this->createMock(
            Application::class
        );

        $app
            ->expects($this->once())
            ->method('make')
            ->with(Dispatcher::class)
            ->willReturn($events);

        $provider = new AuditServiceProvider($app);

        $provider->boot();
    }
}
