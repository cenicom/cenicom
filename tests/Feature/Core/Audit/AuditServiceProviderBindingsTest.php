<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\AuditRecorder;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\Persistence\EloquentAuditRepository;
use Tests\TestCase;

final class AuditServiceProviderBindingsTest extends TestCase
{
    public function test_resolves_audit_recorder_from_container(): void
    {
        $recorder = $this->app->make(
            AuditRecorderInterface::class
        );

        $this->assertInstanceOf(
            AuditRecorder::class,
            $recorder
        );
    }

    public function test_resolves_audit_repository_from_container(): void
    {
        $repository = $this->app->make(
            AuditRepositoryInterface::class
        );

        $this->assertInstanceOf(
            EloquentAuditRepository::class,
            $repository
        );
    }
}
