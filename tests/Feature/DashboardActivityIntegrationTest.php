<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Activity\DTO\ActivityReadModel;
use App\Modules\Institution\Actions\InstitutionAction;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class DashboardActivityIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_institution_creation_activity_appears_on_dashboard(): void
    {
        $user = User::factory()->create([
            'first_name' => 'D03',
            'last_name' => 'Dashboard',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        $this->mock(
            InstitutionIdGeneratorInterface::class,
            function ($mock): void {
                $mock
                    ->shouldReceive('generate')
                    ->once()
                    ->andReturn(
                        '01K3D03A80000000000000020',
                    );
            },
        );

        $this->mock(
            InstitutionCodeGeneratorInterface::class,
            function ($mock): void {
                $mock
                    ->shouldReceive('generate')
                    ->once()
                    ->andReturn(
                        'CEN-D03-DASH-001',
                    );
            },
        );

        /** @var InstitutionAction $action */
        $action = app(InstitutionAction::class);

        $institution = $action->create([
            'name' => 'Institución Dashboard D03',
        ]);

        $response = $this->get('/dashboard');

        $response
            ->assertOk()
            ->assertViewIs('dashboard')
            ->assertViewHas('activities');

        /** @var iterable<ActivityReadModel> $activities */
        $activities = $response->viewData('activities');

        $activities = is_array($activities)
            ? $activities
            : iterator_to_array($activities);

        $matchingActivities = array_values(
            array_filter(
                $activities,
                static fn (ActivityReadModel $activity): bool =>
                    $activity->eventType === 'institution.created'
                    && $activity->subjectType === 'institution'
                    && $activity->subjectId === $institution->id()
                    && ($activity->data['name'] ?? null)
                        === 'Institución Dashboard D03',
            ),
        );

        $this->assertCount(1, $matchingActivities);

        $response->assertSee('Actividades recientes');
        $response->assertSee('institution.created');
    }
}
