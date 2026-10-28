<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearHttpActivationPermissionDeniedTest extends AcademicYear02TestCase
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
        $identity->shouldReceive('id')->andReturn('aca-year02-activation-denied');
        $identity->shouldReceive('name')->andReturn('ACA activation denied test');
        $identity->shouldReceive('roles')->andReturn([]);
        $identity->shouldReceive('permissions')->andReturn([]);
        $identity->shouldReceive('can')
            ->with('academic_year.activate')
            ->andReturn(false);
        $identity->shouldReceive('authenticated')->andReturn(true);

        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(AuthorizationServiceInterface::class);
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.activate')
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

    public function test_request_without_activation_permission_is_forbidden(): void
    {
        $response = $this->post(route('academic-years.activate', [
            'academicYearId' => '01J00000000000000000000000',
        ]));

        $response->assertForbidden();

        $this->assertSame(
            0,
            DB::connection('mysql')
                ->table('audit_entries')
                ->where('action', 'academic_year.activated')
                ->where('actor_id', 'aca-year02-activation-denied')
                ->count(),
        );
    }
}
