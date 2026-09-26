<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Institution;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditEntryData;
use App\Modules\Institution\Actions\InstitutionAction;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Models\User;
use App\Models\AuditLog;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class InstitutionActivityAuditIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_institution_creation_flows_from_producer_to_activity_reader(): void
    {
        $user = User::factory()->create([
            'user_name' => 'D03A8AUDITOR',
            'first_name' => 'D03 A8',
            'last_name' => 'Auditor',
        ]);

        Auth::login($user);

        $authenticatedUser = Auth::user();

        self::assertNotNull(
            $authenticatedUser,
        );

        self::assertSame(
            (string) $user->getKey(),
            (string) $authenticatedUser->getKey(),
        );

        self::assertSame(
            (string) $user->getKey(),
            (string) $authenticatedUser->getAuthIdentifier(),
        );

        self::assertSame(
            $user->name,
            $authenticatedUser->name,
        );

        $identityService = app(
            \App\Core\Security\Services\IdentityService::class
        );

        $identityData = $identityService->current();

        self::assertSame(
            (string) $user->getKey(),
            (string) $identityData->id,
        );

        self::assertSame(
            'D03 A8 Auditor',
            $identityData->name,
        );

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        self::assertSame(
            (string) $user->getKey(),
            (string) $identity->id(),
        );

        self::assertSame(
            'D03 A8 Auditor',
            $identity->name(),
        );

        self::assertTrue(
            $identity->authenticated(),
        );

        $this->mock(
            InstitutionIdGeneratorInterface::class,
            function ($mock): void {
                $mock->shouldReceive('generate')
                    ->once()
                    ->andReturn('01K3D03A80000000000000010');
            },
        );

        $this->mock(
            InstitutionCodeGeneratorInterface::class,
            function ($mock): void {
                $mock->shouldReceive('generate')
                    ->once()
                    ->andReturn('CEN-D03-A8-001');
            },
        );

        $before = new DateTimeImmutable();

        /** @var InstitutionAction $action */
        $action = app(InstitutionAction::class);

        $institution = $action->create([
            'name' => 'Institución D03 A8',
        ]);

        $audit = AuditLog::query()
            ->where('action', 'institution.created')
            ->latest('id')
            ->firstOrFail();

        self::assertSame(
            'D03 A8 Auditor',
            $audit->actor_name,
        );

        $after = new DateTimeImmutable();

        self::assertDatabaseHas(
            'institutions',
            [
                'id' => $institution->id(),
                'name' => 'Institución D03 A8',
            ],
        );

        /** @var AuditQueryInterface $query */
        $query = app(AuditQueryInterface::class);

        $activities = $this->readRecentInstitutionActivities(
            $query,
            10,
        );

        self::assertCount(1, $activities);

        $activity = $activities[0];

        self::assertSame(
            'institution.created',
            $activity->action,
        );

        self::assertSame(
            (string) $user->getKey(),
            $activity->actorId,
        );

        self::assertSame(
            'D03 A8 Auditor',
            $activity->actorName,
        );

        self::assertTrue(
            $activity->actorAuthenticated,
        );

        self::assertSame(
            'institution',
            $activity->subjectType,
        );

        self::assertSame(
            $institution->id(),
            $activity->subjectId,
        );

        self::assertSame(
            'Institución D03 A8',
            $activity->metadata['name'] ?? null,
        );

        self::assertSame(
            'success',
            $activity->result,
        );

        self::assertGreaterThanOrEqual(
            $before->getTimestamp(),
            $activity->occurredAt->getTimestamp(),
        );

        self::assertLessThanOrEqual(
            $after->getTimestamp(),
            $activity->occurredAt->getTimestamp(),
        );
    }

    /**
     * D-03.A.7 contractual composition over the existing AuditQueryInterface.
     *
     * This is deliberately a test-level reader proof. It is not yet the
     * production Dashboard reader; its purpose is to demonstrate that the
     * first business activity can be read without changing AuditQueryInterface.
     *
     * @return array<int, AuditEntryData>
     */
    private function readRecentInstitutionActivities(
        AuditQueryInterface $query,
        int $limit,
    ): array {
        $from = new DateTimeImmutable('-1 day');
        $to = new DateTimeImmutable('+1 second');

        $entries = [];

        foreach ($query->between($from, $to) as $entry) {
            if ($entry->action !== 'institution.created') {
                continue;
            }

            $entries[] = $entry;
        }

        usort(
            $entries,
            static fn(AuditEntryData $left, AuditEntryData $right): int
            => $right->occurredAt <=> $left->occurredAt,
        );

        return array_slice($entries, 0, $limit);
    }
}
