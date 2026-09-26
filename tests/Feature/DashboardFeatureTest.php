<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Core\Activity\DTO\ActivityReadModel;
use App\Models\User;
use App\Modules\Institution\Actions\InstitutionAction;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_unverified_user_is_redirected_to_email_verification(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertRedirect('/email/verify');
    }

    public function test_verified_user_can_view_dashboard_with_recent_activities(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $activity = new ActivityReadModel(
            eventType: 'institution.created',
            occurredAt: new DateTimeImmutable(
                '2026-09-25 10:30:00',
            ),
            actorId: (string) $user->getAuthIdentifier(),
            actorName: 'Test User',
            actorAuthenticated: true,
            subjectType: 'institution',
            subjectId: '01JTESTINSTITUTION000000000001',
            data: [
                'name' => 'Escuela Batalla de Boyacá',
            ],
            result: 'success',
        );

        $reader = Mockery::mock(
            RecentActivityReaderInterface::class,
        );

        $reader
            ->shouldReceive('recent')
            ->once()
            ->with(5)
            ->andReturn([$activity]);

        $this->app->instance(
            RecentActivityReaderInterface::class,
            $reader,
        );

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('activities')
            ->assertSee('Actividades recientes')
            ->assertSee('institution.created')
            ->assertSee('Test User');
    }

    public function test_verified_user_can_view_total_institutions_on_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $reader = Mockery::mock(
            RecentActivityReaderInterface::class,
        );

        $reader
            ->shouldReceive('recent')
            ->once()
            ->with(5)
            ->andReturn([]);

        $this->app->instance(
            RecentActivityReaderInterface::class,
            $reader,
        );

        $institutions = Mockery::mock(
            InstitutionServiceInterface::class,
        );

        $institutions
            ->shouldReceive('count')
            ->once()
            ->andReturn(7);

        $this->app->instance(
            InstitutionServiceInterface::class,
            $institutions,
        );

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('totalInstitutions', 7);
    }

    public function test_verified_user_can_view_real_total_institutions_on_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $reader = Mockery::mock(
            RecentActivityReaderInterface::class,
        );

        $reader
            ->shouldReceive('recent')
            ->once()
            ->with(5)
            ->andReturn([]);

        $this->app->instance(
            RecentActivityReaderInterface::class,
            $reader,
        );

        $this->mock(
            InstitutionIdGeneratorInterface::class,
            function ($mock): void {
                $mock
                    ->shouldReceive('generate')
                    ->twice()
                    ->andReturn(
                        '01K3D03REAL000000000000001',
                        '01K3D03REAL000000000000002',
                    );
            },
        );

        $this->mock(
            InstitutionCodeGeneratorInterface::class,
            function ($mock): void {
                $mock
                    ->shouldReceive('generate')
                    ->twice()
                    ->andReturn(
                        'CEN-D03-REAL-001',
                        'CEN-D03-REAL-002',
                    );
            },
        );

        /** @var InstitutionAction $action */
        $action = app(InstitutionAction::class);

        $action->create([
            'name' => 'Institución Dashboard Real 001',
        ]);

        $action->create([
            'name' => 'Institución Dashboard Real 002',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('totalInstitutions', 2);
    }
}
