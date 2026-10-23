<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\Services\AcademicYearActivator;
use App\Modules\Academic\Domain\Services\AcademicYearCloser;
use App\Modules\Academic\Domain\Services\CompleteAcademicYearEvent;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearLifecycleTest extends AcademicYear02TestCase
{
    use DatabaseTransactions;

    private array $institutionIds = [];
    private array $authorityIds = [];
    private array $publicationIds = [];
    private array $versionIds = [];
    private array $yearIds = [];
    private bool $authenticatedIdentity = true;
    private array $grantedPermissions = [
        'academic_year.activate',
        'academic_year.close',
        'academic_year.complete_milestone',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $identity = Mockery::mock(IdentityInterface::class);
        $identity->shouldReceive('id')->andReturn('aca-year02-lifecycle-actor');
        $identity->shouldReceive('name')->andReturn('ACA-YEAR-02 lifecycle test actor');
        $identity->shouldReceive('authenticated')->andReturnUsing(fn (): bool => $this->authenticatedIdentity);
        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(AuthorizationServiceInterface::class);
        $authorization->shouldReceive('can')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function (IdentityInterface $candidate, string $permission): bool {
                return in_array($permission, $this->grantedPermissions, true);
            });
        $this->app->instance(AuthorizationServiceInterface::class, $authorization);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_activates_draft_and_writes_audit_in_same_transaction(): void
    {
        Carbon::setTestNow('2026-01-20 10:00:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-A');

        app(AcademicYearActivator::class)->execute($yearId);

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'active',
            'activated_at' => '2026-01-20 10:00:00',
            'closed_at' => null,
        ], 'mysql');
        $this->assertSame(1, DB::table('audit_entries')
            ->where('action', 'academic_year.activated')
            ->where('subject_id', $yearId)
            ->count());

        $audit = DB::table('audit_entries')
            ->where('action', 'academic_year.activated')
            ->where('subject_id', $yearId)
            ->first();
        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('draft', $metadata['status_before']);
        $this->assertSame('active', $metadata['status_after']);
    }

    public function test_activation_is_rejected_while_any_activation_prerequisite_is_incomplete(): void
    {
        Carbon::setTestNow('2026-01-20 10:00:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-B');
        $this->createEvent($versionId, 'TEACHER_PREP', true, false);

        $this->assertDomainException(
            fn () => app(AcademicYearActivator::class)->execute($yearId),
            'TEACHER_PREP',
        );

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'draft'], 'mysql');
    }

    public function test_activation_succeeds_after_required_milestone_is_completed(): void
    {
        Carbon::setTestNow('2026-01-20 10:00:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-C');
        $eventId = $this->createEvent($versionId, 'TEACHER_PREP', true, false);

        app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId);
        app(AcademicYearActivator::class)->execute($yearId);

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'active'], 'mysql');
        $this->assertDatabaseHas('academic_year_event_completions', [
            'academic_year_id' => $yearId,
            'event_id' => $eventId,
            'completed_by' => 'aca-year02-lifecycle-actor',
        ], 'mysql');

        // ACA-F06: verify the milestone completion and its audit record are persisted together.
        $completion = DB::table('academic_year_event_completions')
            ->where('academic_year_id', $yearId)
            ->where('event_id', $eventId)
            ->first();
        $this->assertNotNull($completion);

        $audit = DB::table('audit_entries')
            ->where('action', 'academic_year.milestone_completed')
            ->where('subject_type', 'academic_year_event_completion')
            ->where('subject_id', $completion->id)
            ->first();

        $this->assertNotNull($audit, 'El cumplimiento del hito debe tener su entrada de auditoría.');
        $this->assertSame('success', $audit->result);
        $this->assertSame('aca-year02-lifecycle-actor', $audit->actor_id);
        $this->assertSame(1, (int) $audit->actor_authenticated);
        $this->assertNotSame('', (string) $audit->occurred_at);

        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($yearId, $metadata['academic_year_id']);
        $this->assertSame($versionId, $metadata['publication_version_id']);
        $this->assertSame($eventId, $metadata['event_id']);
        $this->assertSame('TEACHER_PREP', $metadata['event_code']);
        $this->assertTrue($metadata['activation_prerequisite']);
        $this->assertFalse($metadata['closure_prerequisite']);
    }

    public function test_audit_failure_rolls_back_milestone_completion(): void
    {
        Carbon::setTestNow('2026-01-20 10:00:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-M');
        $eventId = $this->createEvent($versionId, 'TEACHER_PREP', true, false);

        $recorder = \Mockery::mock(\App\Core\Audit\Contracts\AuditRecorderInterface::class);
        $recorder->shouldReceive('record')
            ->once()
            ->andThrow(new \RuntimeException('Fallo de auditoría de cumplimiento de hito.'));
        $this->app->instance(\App\Core\Audit\Contracts\AuditRecorderInterface::class, $recorder);

        try {
            app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId);
            $this->fail('El fallo del registro de auditoría debía propagarse.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Fallo de auditoría de cumplimiento de hito.', $exception->getMessage());
        }

        $this->assertDatabaseMissing('academic_year_event_completions', [
            'academic_year_id' => $yearId,
            'event_id' => $eventId,
        ], 'mysql');
    }
    public function test_activation_requires_permission_and_authenticated_identity(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-D');
        $this->grantedPermissions = [];

        try {
            app(AcademicYearActivator::class)->execute($yearId);
            $this->fail('Debía rechazarse la activación sin permiso.');
        } catch (AuthorizationException) {
            $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'draft'], 'mysql');
        }

        $this->grantedPermissions = ['academic_year.activate'];
        $this->authenticatedIdentity = false;

        try {
            app(AcademicYearActivator::class)->execute($yearId);
            $this->fail('Debía rechazarse la activación de una identidad no autenticada.');
        } catch (AuthorizationException) {
            $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'draft'], 'mysql');
        }
    }

    public function test_closes_active_year_only_after_official_end_and_complete_close_prerequisites(): void
    {
        Carbon::setTestNow('2026-12-17 11:30:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-E', 'active');
        $eventId = $this->createEvent($versionId, 'FINAL_REPORTS', false, true);
        app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId);

        app(AcademicYearCloser::class)->execute($yearId);

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'closed',
            'closed_at' => '2026-12-17 11:30:00',
        ], 'mysql');
        $this->assertSame(1, DB::table('audit_entries')
            ->where('action', 'academic_year.closed')
            ->where('subject_id', $yearId)
            ->count());
    }

    public function test_closure_is_rejected_on_or_before_official_end_date(): void
    {
        Carbon::setTestNow('2026-12-15 23:59:59');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-F', 'active');

        $this->assertDomainException(
            fn () => app(AcademicYearCloser::class)->execute($yearId),
            'después del fin oficial',
        );

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'active'], 'mysql');
    }

    public function test_closure_is_rejected_until_all_closure_prerequisites_are_completed(): void
    {
        Carbon::setTestNow('2026-12-17 11:30:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-G', 'active');
        $this->createEvent($versionId, 'FINAL_REPORTS', false, true);

        $this->assertDomainException(
            fn () => app(AcademicYearCloser::class)->execute($yearId),
            'FINAL_REPORTS',
        );

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'active'], 'mysql');
    }

    public function test_closed_year_cannot_receive_milestone_completion(): void
    {
        Carbon::setTestNow('2026-12-17 11:30:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $closedId = $this->createYear($institutionId, $versionId, '2026-H', 'closed');
        $eventId = $this->createEvent($versionId, 'FINAL_REPORTS', false, true);

        $this->assertDomainException(
            fn () => app(CompleteAcademicYearEvent::class)->execute($closedId, $eventId),
            'draft o active',
        );
    }

    public function test_completion_rejects_event_from_another_publication_version(): void
    {
        [$institutionId, $firstVersionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        [, $secondVersionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $firstVersionId, '2026-I');
        $eventId = $this->createEvent($secondVersionId, 'FINAL_REPORTS', false, true);

        $this->assertDomainException(
            fn () => app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId),
            'no pertenece',
        );
    }

    public function test_completion_rejects_event_not_marked_as_lifecycle_prerequisite(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-J');
        $eventId = $this->createEvent($versionId, 'OPTIONAL_RECESS', false, false);

        $this->assertDomainException(
            fn () => app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId),
            'no está declarado como prerrequisito',
        );
    }

    public function test_completion_cannot_be_registered_twice(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-K');
        $eventId = $this->createEvent($versionId, 'TEACHER_PREP', true, false);
        app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId);

        $this->assertDomainException(
            fn () => app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId),
            'ya está registrado',
        );

        $this->assertSame(1, DB::table('academic_year_event_completions')
            ->where('academic_year_id', $yearId)
            ->where('event_id', $eventId)
            ->count());
    }


    public function test_activation_rejects_version_that_is_no_longer_available(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-M');
        DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->update(['verification_status' => 'validated', 'updated_at' => now()]);

        $this->assertDomainException(
            fn () => app(AcademicYearActivator::class)->execute($yearId),
            'debe seguir disponible',
        );

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'draft'], 'mysql');
    }

    public function test_closure_rejects_a_draft_year(): void
    {
        Carbon::setTestNow('2026-12-17 11:30:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-N');

        $this->assertDomainException(
            fn () => app(AcademicYearCloser::class)->execute($yearId),
            'estado active',
        );

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'draft'], 'mysql');
    }

    public function test_audit_failure_rolls_back_lifecycle_transition(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-L');

        $recorder = Mockery::mock(AuditRecorderInterface::class);
        $recorder->shouldReceive('record')->once()->andThrow(new RuntimeException('audit fixture failure'));
        $this->app->instance(AuditRecorderInterface::class, $recorder);

        try {
            app(AcademicYearActivator::class)->execute($yearId);
            $this->fail('El fallo de auditoría debía revertir la activación.');
        } catch (RuntimeException $exception) {
            $this->assertSame('audit fixture failure', $exception->getMessage());
        }

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'draft',
            'activated_at' => null,
        ], 'mysql');
    }

    private function createInstitutionAndVersion(string $startDate, string $endDate): array
    {
        $institutionId = (string) Str::ulid();
        $authorityId = (string) Str::ulid();
        $publicationId = (string) Str::ulid();
        $versionId = (string) Str::ulid();
        $shortCode = $this->uniqueLetters(8);
        $now = now();

        DB::table('institutions')->insert([
            'id' => $institutionId,
            'code' => 'ACA-' . substr($institutionId, 0, 12),
            'short_code' => $shortCode,
            'name' => 'Institución de prueba ACA-F02',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('academic_regulatory_authorities')->insert([
            'id' => $authorityId,
            'country_code' => 'CO',
            'authority_code' => 'TEST-' . substr($authorityId, 0, 12),
            'official_name' => 'Autoridad de prueba ACA-F02',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publications')->insert([
            'id' => $publicationId,
            'authority_id' => $authorityId,
            'document_type' => 'RESOLUTION',
            'document_number' => substr($publicationId, 0, 8),
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'ACA_TEST',
            'regime_code' => 'GENERAL',
            'region_code' => 'TEST_REGION',
            'calendar_code' => 'A',
            'identity_status' => 'confirmed',
            'identity_fingerprint' => hash('sha256', $publicationId),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $versionId,
            'publication_id' => $publicationId,
            'version_number' => 1,
            'supersedes_version_id' => null,
            'official_version_label' => null,
            'authority_name_snapshot' => 'Autoridad de prueba ACA-F02',
            'title' => 'Calendario de pruebas ACA-F02',
            'promulgated_on' => '2026-01-01',
            'calendar_start_date' => $startDate,
            'calendar_end_date' => $endDate,
            'source_url' => 'https://example.invalid/aca-year02-f02-test',
            'source_reference' => 'Fixture exclusiva de pruebas',
            'document_sha256' => null,
            'verification_status' => 'available',
            'validated_at' => $now,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->institutionIds[] = $institutionId;
        $this->authorityIds[] = $authorityId;
        $this->publicationIds[] = $publicationId;
        $this->versionIds[] = $versionId;

        return [$institutionId, $versionId];
    }

    private function createYear(
        string $institutionId,
        string $versionId,
        string $cycleCode,
        string $status = 'draft',
    ): string {
        $id = (string) Str::ulid();
        $now = now();

        DB::table('academic_years')->insert([
            'id' => $id,
            'institution_id' => $institutionId,
            'publication_version_id' => $versionId,
            'cycle_code' => $cycleCode,
            'calendar_code' => 'A',
            'status' => $status,
            'activated_at' => in_array($status, ['active', 'closed'], true) ? $now : null,
            'closed_at' => $status === 'closed' ? $now : null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->yearIds[] = $id;

        return $id;
    }

    private function createEvent(
        string $versionId,
        string $code,
        bool $activationPrerequisite,
        bool $closurePrerequisite,
    ): string {
        $id = (string) Str::ulid();
        $now = now();

        DB::table('academic_calendar_events')->insert([
            'id' => $id,
            'publication_version_id' => $versionId,
            'academic_period_id' => null,
            'code' => $code,
            'event_type' => 'INSTITUTIONAL_DEVELOPMENT',
            'title' => 'Hito ' . $code,
            'audience' => 'ALL',
            'start_date' => '2026-01-12',
            'end_date' => '2026-01-15',
            'is_officially_required' => true,
            'activation_prerequisite' => $activationPrerequisite,
            'closure_prerequisite' => $closurePrerequisite,
            'source_reference' => 'Fixture de prueba ACA-F02',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }

    private function assertDomainException(callable $operation, string $messageFragment): void
    {
        try {
            $operation();
            $this->fail('Se esperaba DomainException.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString($messageFragment, $exception->getMessage());
        }
    }

    private function uniqueLetters(int $length): string
    {
        do {
            $value = '';
            for ($index = 0; $index < $length; $index++) {
                $value .= chr(random_int(65, 90));
            }
        } while (DB::table('institutions')->where('short_code', $value)->exists());

        return $value;
    }
}
