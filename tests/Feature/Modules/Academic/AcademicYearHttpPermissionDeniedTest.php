<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearHttpPermissionDeniedTest extends AcademicYear02TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'array']);

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $identity = Mockery::mock(IdentityInterface::class);

        $identity->shouldReceive('id')
            ->andReturn('aca-year02-denied-test-actor');

        $identity->shouldReceive('name')
            ->andReturn('ACA-YEAR-02 denied HTTP test actor');

        $identity->shouldReceive('roles')->andReturn([]);
        $identity->shouldReceive('permissions')->andReturn([]);
        $identity->shouldReceive('can')
            ->with('academic_year.create')
            ->andReturn(false);

        $identity->shouldReceive('authenticated')->andReturn(true);

        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(
            AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.create')
            ->andReturn(false);

        $this->app->instance(
            AuthorizationServiceInterface::class,
            $authorization,
        );

        $this->withoutMiddleware(
            \Illuminate\Auth\Middleware\Authenticate::class,
        );

        $this->withoutMiddleware(
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        );
    }

    public function test_request_without_create_permission_is_forbidden(): void
    {
        $response = $this->post(route('academic-years.store'), [
            'institutionId' => 'aca-year02-denied-nonexistent-institution',
            'publicationVersionId' => 'aca-year02-denied-nonexistent-version',
            'cycleCode' => '2026',
            'calendarCode' => 'A',
        ]);

        $response->assertForbidden();

        $this->assertSame(
            0,
            DB::connection('mysql')
                ->table('academic_years')
                ->where('cycle_code', '2026')
                ->count(),
        );

        $this->assertSame(
            0,
            DB::connection('mysql')
                ->table('audit_entries')
                ->where('action', 'academic_year.created')
                ->where('actor_id', 'aca-year02-denied-test-actor')
                ->where('result', 'success')
                ->count(),
        );
    }
}
