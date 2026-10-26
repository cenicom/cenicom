<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicCalendarEventData;
use App\Modules\Academic\Domain\Services\CreateAcademicCalendarEvent;
use App\Modules\Academic\Domain\Services\UpdateAcademicCalendarEvent;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use Tests\Support\AcademicYear02TestCase;

final class AcademicCalendarEventManagementTest extends AcademicYear02TestCase
{
    private string $publicationVersionId;
    private int $transactionLevelBefore = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $this->transactionLevelBefore = DB::connection('mysql')->transactionLevel();
        DB::connection('mysql')->beginTransaction();

        $identity = Mockery::mock(IdentityInterface::class);
        $identity->shouldReceive('id')->andReturn('aca-year02-event-actor');
        $identity->shouldReceive('name')->andReturn('ACA-YEAR-02 event test actor');
        $identity->shouldReceive('authenticated')->andReturn(true);
        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(AuthorizationServiceInterface::class);
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_calendar_event.create')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_calendar_event.update')
            ->andReturn(true);
        $this->app->instance(AuthorizationServiceInterface::class, $authorization);

        $this->publicationVersionId = $this->createDraftPublicationVersion();
    }

    protected function tearDown(): void
    {
        try {
            if (DB::connection('mysql')->getDatabaseName() === 'cenicom_aca_year02_isolated_test') {
                while (DB::connection('mysql')->transactionLevel() > $this->transactionLevelBefore) {
                    DB::connection('mysql')->rollBack();
                }
            }
        } finally {
            parent::tearDown();
        }
    }

    public function test_event_create_and_update_permissions_are_registered(): void
    {
        $registry = app(\App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class);
        $create = $registry->permission('academic_calendar_event.create');
        $update = $registry->permission('academic_calendar_event.update');

        $this->assertNotNull($create);
        $this->assertSame('academic_calendar_event.create', $create->key());
        $this->assertNotNull($update);
        $this->assertSame('academic_calendar_event.update', $update->key());
    }

    public function test_creates_general_event_without_period_and_keeps_three_flags_distinct(): void
    {
        $data = $this->eventData(
            code: 'PED-DAY',
            eventType: 'PEDAGOGICAL_DAY',
            title: 'Jornada pedagógica',
            startDate: '2026-04-01',
            endDate: '2026-04-02',
            periodId: null,
            officiallyRequired: true,
            activationPrerequisite: false,
            closurePrerequisite: true,
        );

        $eventId = app(CreateAcademicCalendarEvent::class)->execute($this->publicationVersionId, $data);

        $this->assertDatabaseHas('academic_calendar_events', [
            'id' => $eventId,
            'publication_version_id' => $this->publicationVersionId,
            'academic_period_id' => null,
            'code' => 'PED-DAY',
            'event_type' => 'PEDAGOGICAL_DAY',
            'is_officially_required' => 1,
            'activation_prerequisite' => 0,
            'closure_prerequisite' => 1,
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'academic_calendar_event')
            ->where('subject_id', $eventId)
            ->where('action', 'academic_calendar_event.created')
            ->where('result', 'success')
            ->first();
        $this->assertNotNull($audit);
        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNull($metadata['old_values']);
        $this->assertTrue($metadata['new_values']['is_officially_required']);
        $this->assertFalse($metadata['new_values']['activation_prerequisite']);
        $this->assertTrue($metadata['new_values']['closure_prerequisite']);
    }

    public function test_creates_exam_interval_only_within_its_period(): void
    {
        $periodId = $this->createPeriod();
        $eventId = app(CreateAcademicCalendarEvent::class)->execute(
            $this->publicationVersionId,
            $this->eventData(
                code: 'EXAM-T1',
                eventType: 'EXAM_INTERVAL',
                title: 'Evaluaciones del primer período',
                startDate: '2026-03-10',
                endDate: '2026-03-15',
                periodId: $periodId,
            ),
        );

        $this->assertDatabaseHas('academic_calendar_events', [
            'id' => $eventId,
            'academic_period_id' => $periodId,
            'event_type' => 'EXAM_INTERVAL',
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-15',
        ], 'mysql');
    }

    public function test_updates_event_and_records_old_and_new_values(): void
    {
        $eventId = app(CreateAcademicCalendarEvent::class)->execute(
            $this->publicationVersionId,
            $this->eventData('PED-DAY', 'PEDAGOGICAL_DAY', 'Jornada inicial', '2026-04-01', '2026-04-02'),
        );

        app(UpdateAcademicCalendarEvent::class)->execute(
            $eventId,
            $this->eventData(
                code: 'PED-DAY',
                eventType: 'PEDAGOGICAL_DAY',
                title: 'Jornada pedagógica actualizada',
                startDate: '2026-04-03',
                endDate: '2026-04-04',
                officiallyRequired: true,
                activationPrerequisite: true,
                closurePrerequisite: false,
                sourceReference: 'RES-2026-01',
            ),
        );

        $this->assertDatabaseHas('academic_calendar_events', [
            'id' => $eventId,
            'title' => 'Jornada pedagógica actualizada',
            'start_date' => '2026-04-03',
            'end_date' => '2026-04-04',
            'is_officially_required' => 1,
            'activation_prerequisite' => 1,
            'closure_prerequisite' => 0,
            'source_reference' => 'RES-2026-01',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'academic_calendar_event')
            ->where('subject_id', $eventId)
            ->where('action', 'academic_calendar_event.updated')
            ->where('result', 'success')
            ->first();
        $this->assertNotNull($audit);
        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Jornada inicial', $metadata['old_values']['title']);
        $this->assertSame('2026-04-01', $metadata['old_values']['start_date']);
        $this->assertSame('Jornada pedagógica actualizada', $metadata['new_values']['title']);
        $this->assertSame('2026-04-03', $metadata['new_values']['start_date']);
    }

    public function test_rejects_exam_without_period_and_does_not_audit_success(): void
    {
        try {
            app(CreateAcademicCalendarEvent::class)->execute(
                $this->publicationVersionId,
                $this->eventData('EXAM-NO-PERIOD', 'EXAM_INTERVAL', 'Examen', '2026-04-01', '2026-04-03'),
            );
            $this->fail('Un intervalo de exámenes no puede existir sin período.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('debe pertenecer a un período académico', $exception->getMessage());
        }

        $this->assertSame(0, DB::table('academic_calendar_events')->count());
        $this->assertSame(0, DB::table('audit_entries')->where('action', 'academic_calendar_event.created')->count());
    }

    public function test_rejects_event_outside_period_and_event_outside_official_window(): void
    {
        $periodId = $this->createPeriod();

        try {
            app(CreateAcademicCalendarEvent::class)->execute(
                $this->publicationVersionId,
                $this->eventData('OUTSIDE-PERIOD', 'EXAM_INTERVAL', 'Examen fuera', '2026-03-30', '2026-04-02', $periodId),
            );
            $this->fail('El intervalo no debe salir del período.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('dentro de las fechas de su período', $exception->getMessage());
        }

        try {
            app(CreateAcademicCalendarEvent::class)->execute(
                $this->publicationVersionId,
                $this->eventData('OUTSIDE-OFFICIAL', 'OTHER', 'Fuera del calendario', '2026-12-15', '2026-12-16'),
            );
            $this->fail('El evento no debe superar el intervalo oficial.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('intervalo oficial', $exception->getMessage());
        }

        $this->assertSame(0, DB::table('academic_calendar_events')->count());
        $this->assertSame(0, DB::table('audit_entries')->where('action', 'academic_calendar_event.created')->count());
    }

    public function test_rejects_duplicate_code_without_creating_second_event(): void
    {
        app(CreateAcademicCalendarEvent::class)->execute(
            $this->publicationVersionId,
            $this->eventData('TERM-START', 'STUDENT_CLASSES_START', 'Inicio de clases', '2026-01-10', '2026-01-10'),
        );

        try {
            app(CreateAcademicCalendarEvent::class)->execute(
                $this->publicationVersionId,
                $this->eventData('TERM-START', 'STUDENT_CLASSES_START', 'Otro inicio', '2026-01-12', '2026-01-12'),
            );
            $this->fail('El código de evento debe ser único en la versión oficial.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('ya existe', $exception->getMessage());
        }

        $this->assertSame(1, DB::table('academic_calendar_events')->count());
        $this->assertSame(1, DB::table('audit_entries')->where('action', 'academic_calendar_event.created')->count());
    }

    public function test_event_cannot_be_created_after_publication_version_leaves_draft(): void
    {
        $at = now();
        DB::table('official_calendar_publication_versions')
            ->where('id', $this->publicationVersionId)
            ->update(['verification_status' => 'validated', 'validated_at' => $at, 'updated_at' => $at]);

        try {
            app(CreateAcademicCalendarEvent::class)->execute(
                $this->publicationVersionId,
                $this->eventData('TERM-START', 'STUDENT_CLASSES_START', 'Inicio de clases', '2026-01-10', '2026-01-10'),
            );
            $this->fail('No debe modificarse la estructura después de validar la versión oficial.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('draft', $exception->getMessage());
        }

        $this->assertSame(0, DB::table('academic_calendar_events')->count());
        $this->assertSame(0, DB::table('audit_entries')->where('action', 'academic_calendar_event.created')->count());
    }

    private function createDraftPublicationVersion(): string
    {
        $authorityId = (string) Str::ulid();
        $publicationId = (string) Str::ulid();
        $versionId = (string) Str::ulid();
        $now = now();

        DB::table('academic_regulatory_authorities')->insert([
            'id' => $authorityId,
            'country_code' => 'CO',
            'authority_code' => 'EVENT-' . substr($authorityId, 0, 12),
            'official_name' => 'Autoridad de prueba para eventos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publications')->insert([
            'id' => $publicationId,
            'authority_id' => $authorityId,
            'document_type' => 'resolution',
            'document_number' => 'EVENT-' . $publicationId,
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'REGULAR',
            'region_code' => 'NATIONAL',
            'calendar_code' => 'A',
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $versionId,
            'publication_id' => $publicationId,
            'version_number' => 1,
            'supersedes_version_id' => null,
            'official_version_label' => 'v1',
            'authority_name_snapshot' => 'Autoridad de prueba para eventos',
            'title' => 'Calendario de prueba para eventos',
            'promulgated_on' => '2026-01-05',
            'calendar_start_date' => '2026-01-10',
            'calendar_end_date' => '2026-12-15',
            'source_url' => 'https://example.invalid/academic-event-test',
            'source_reference' => 'ACA-YEAR02-EVENT-FIXTURE',
            'document_sha256' => str_repeat('e', 64),
            'verification_status' => 'draft',
            'validated_at' => null,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $versionId;
    }

    private function createPeriod(): string
    {
        $periodId = (string) Str::ulid();
        $now = now();
        DB::table('academic_periods')->insert([
            'id' => $periodId,
            'publication_version_id' => $this->publicationVersionId,
            'code' => 'P1',
            'name' => 'Primer período',
            'sequence_number' => 1,
            'start_date' => '2026-01-10',
            'end_date' => '2026-03-31',
            'declared_duration' => null,
            'duration_unit' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $periodId;
    }

    private function eventData(
        string $code,
        string $eventType,
        string $title,
        string $startDate,
        string $endDate,
        ?string $periodId = null,
        bool $officiallyRequired = false,
        bool $activationPrerequisite = false,
        bool $closurePrerequisite = false,
        ?string $sourceReference = null,
        string $audience = 'ALL',
    ): AcademicCalendarEventData {
        return new AcademicCalendarEventData(
            code: $code,
            eventType: $eventType,
            title: $title,
            audience: $audience,
            startDate: $startDate,
            endDate: $endDate,
            academicPeriodId: $periodId,
            isOfficiallyRequired: $officiallyRequired,
            activationPrerequisite: $activationPrerequisite,
            closurePrerequisite: $closurePrerequisite,
            sourceReference: $sourceReference,
        );
    }
}
