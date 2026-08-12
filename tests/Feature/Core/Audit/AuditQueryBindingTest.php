<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Audit;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\Persistence\EloquentAuditQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditQueryBindingTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_audit_query_from_container(): void
    {
        $query = $this->app->make(
            AuditQueryInterface::class
        );

        $this->assertInstanceOf(
            EloquentAuditQuery::class,
            $query
        );
    }
}
