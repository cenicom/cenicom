<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Audit\Providers;

use App\Core\Audit\AuditRecorder;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\Persistence\EloquentAuditRepository;
use App\Core\Audit\Providers\AuditServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\TestCase;

final class AuditServiceProviderBindingsTest extends TestCase
{
    public function test_resolves_audit_recorder(): void
    {
        $app = $this->createMock(Application::class);

        $app
            ->expects($this->once())
            ->method('make')
            ->with(AuditRecorderInterface::class)
            ->willReturn(
                new AuditRecorder(
                    $this->createMock(
                        AuditRepositoryInterface::class
                    )
                )
            );

        $provider = new AuditServiceProvider($app);

        $this->assertInstanceOf(
            AuditRecorder::class,
            $app->make(AuditRecorderInterface::class)
        );
    }

    public function test_resolves_audit_repository(): void
    {
        $app = $this->createMock(Application::class);

        $app
            ->expects($this->once())
            ->method('make')
            ->with(AuditRepositoryInterface::class)
            ->willReturn(
                new EloquentAuditRepository()
            );

        $provider = new AuditServiceProvider($app);

        $this->assertInstanceOf(
            EloquentAuditRepository::class,
            $app->make(AuditRepositoryInterface::class)
        );
    }
}
