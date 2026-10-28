<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicPeriodData;
use App\Modules\Academic\Domain\Services\CreateAcademicPeriod;
use App\Modules\Academic\Domain\Services\UpdateAcademicPeriod;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use Tests\Support\AcademicYear02TestCase;

final class AcademicPeriodManagementTest extends AcademicYear02TestCase
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
        $identity->shouldReceive('id')->andReturn('aca-year02-period-actor');
        $identity->shouldReceive('name')->andReturn('ACA-YEAR-02 period test actor');
        $identity->shouldReceive('authenticated')->andReturn(true);
        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(AuthorizationServiceInterface::class);
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_period.create')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_period.update')
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

    public function test_period_create_and_update_permissions_are_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $create = $registry->permission('academic_period.create');
        $update = $registry->permission('academic_period.update');

        $this->assertNotNull($create);
        $this->assertSame('academic_period.create', $create->key());
        $this->assertNotNull($update);
        $this->assertSame('academic_period.update', $update->key());
    }

    public function test_creates_period_and_records_audit(): void
    {
        $data = $this->periodData(
            code: 'T1',
            name: 'Primer trimestre',
            sequenceNumber: 1,
            startDate: '2026-01-10',
            endDate: '2026-03-31',
            duration: '81.00',
            unit: 'DAYS',
        );

        $periodId = app(CreateAcademicPeriod::class)->execute(
            $this->publicationVersionId,
            $data,
        );

        $this->assertDatabaseHas('academic_periods', [
            'id' => $periodId,
            'publication_version_id' => $this->publicationVersionId,
            'code' => 'T1',
            'name' => 'Primer trimestre',
            'sequence_number' => 1,
            'start_date' => '2026-01-10',
            'end_date' => '2026-03-31',
            'declared_duration' => '81.00',
            'duration_unit' => 'DAYS',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'academic_period')
            ->where('subject_id', $periodId)
            ->where('action', 'academic_period.created')
            ->where('result', 'success')
            ->first();

        $this->assertNotNull($audit);
        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNull($metadata['old_values']);
        $this->assertSame('T1', $metadata['new_values']['code']);
        $this->assertSame('2026-01-10', $metadata['new_values']['start_date']);
    }

    public function test_updates_period_and_records_old_and_new_values(): void
    {
        $periodId = app(CreateAcademicPeriod::class)->execute(
            $this->publicationVersionId,
            $this->periodData('T1', 'Primer trimestre', 1, '2026-01-10', '2026-03-31', '81.00', 'DAYS'),
        );

        app(UpdateAcademicPeriod::class)->execute(
            $periodId,
            $this->periodData('T1', 'Primer período', 1, '2026-01-11', '2026-03-31', '80.00', 'DAYS'),
        );

        $this->assertDatabaseHas('academic_periods', [
            'id' => $periodId,
            'name' => 'Primer período',
            'start_date' => '2026-01-11',
            'declared_duration' => '80.00',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'academic_period')
            ->where('subject_id', $periodId)
            ->where('action', 'academic_period.updated')
            ->where('result', 'success')
            ->first();

        $this->assertNotNull($audit);
        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Primer trimestre', $metadata['old_values']['name']);
        $this->assertSame('2026-01-10', $metadata['old_values']['start_date']);
        $this->assertSame('Primer período', $metadata['new_values']['name']);
        $this->assertSame('2026-01-11', $metadata['new_values']['start_date']);
    }

    public function test_rejects_period_update_that_would_leave_existing_event_outside(): void
    {
        $periodId = app(CreateAcademicPeriod::class)->execute(
            $this->publicationVersionId,
            $this->periodData(
                'T1',
                'Primer trimestre',
                1,
                '2026-01-10',
                '2026-03-31',
                '81.00',
                'DAYS',
            ),
        );

        $eventId = (string) Str::ulid();
        $now = now();

        DB::table('academic_calendar_events')->insert([
            'id' => $eventId,
            'publication_version_id' => $this->publicationVersionId,
            'academic_period_id' => $periodId,
            'code' => 'EVENT-' . substr($eventId, -8),
            'event_type' => 'OTHER',
            'title' => 'Evento asociado al período',
            'audience' => 'ALL',
            'start_date' => '2026-01-15',
            'end_date' => '2026-01-16',
            'is_officially_required' => false,
            'activation_prerequisite' => false,
            'closure_prerequisite' => false,
            'source_reference' => 'ACA-F07-EVENT-' . substr($eventId, -8),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        try {
            app(UpdateAcademicPeriod::class)->execute(
                $periodId,
                $this->periodData(
                    'T1',
                    'Primer trimestre',
                    1,
                    '2026-01-20',
                    '2026-03-31',
                    '72.00',
                    'DAYS',
                ),
            );

            $this->fail(
                'No debe permitirse actualizar un período dejando un evento asociado fuera de sus fechas.',
            );
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'eventos asociados fuera de sus fechas',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('academic_periods', [
            'id' => $periodId,
            'start_date' => '2026-01-10',
            'end_date' => '2026-03-31',
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'academic_period')
                ->where('subject_id', $periodId)
                ->where('action', 'academic_period.updated')
                ->where('result', 'success')
                ->count(),
        );
    }
    public function test_rejects_inclusive_overlap_without_creating_second_period_or_audit(): void
    {
        app(CreateAcademicPeriod::class)->execute(
            $this->publicationVersionId,
            $this->periodData('T1', 'Primer trimestre', 1, '2026-01-10', '2026-03-31'),
        );

        try {
            app(CreateAcademicPeriod::class)->execute(
                $this->publicationVersionId,
                $this->periodData('T2', 'Segundo trimestre', 2, '2026-03-31', '2026-06-30'),
            );
            $this->fail('Los períodos con un día de intersección no deben aceptarse.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('se solapa', $exception->getMessage());
        }

        $this->assertSame(
            1,
            DB::table('academic_periods')
                ->where('publication_version_id', $this->publicationVersionId)
                ->count(),
        );
        $this->assertSame(
            1,
            DB::table('audit_entries')
                ->where('action', 'academic_period.created')
                ->count(),
        );
    }

    public function test_period_cannot_be_created_after_publication_version_leaves_draft(): void
    {
        $at = now();
        DB::table('official_calendar_publication_versions')
            ->where('id', $this->publicationVersionId)
            ->update([
                'verification_status' => 'validated',
                'validated_at' => $at,
                'updated_at' => $at,
            ]);

        try {
            app(CreateAcademicPeriod::class)->execute(
                $this->publicationVersionId,
                $this->periodData('T1', 'Primer trimestre', 1, '2026-01-10', '2026-03-31'),
            );
            $this->fail('No debe modificarse la estructura después de validar la versión oficial.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString('draft', $exception->getMessage());
        }

        $this->assertSame(0, DB::table('academic_periods')->count());
        $this->assertSame(
            0,
            DB::table('audit_entries')->where('action', 'academic_period.created')->count(),
        );
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
            'authority_code' => 'PERIOD-' . substr($authorityId, 0, 12),
            'official_name' => 'Autoridad de prueba para períodos',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publications')->insert([
            'id' => $publicationId,
            'authority_id' => $authorityId,
            'document_type' => 'resolution',
            'document_number' => 'PERIOD-' . $publicationId,
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
            'authority_name_snapshot' => 'Autoridad de prueba para períodos',
            'title' => 'Calendario de prueba para períodos',
            'promulgated_on' => '2026-01-05',
            'calendar_start_date' => '2026-01-10',
            'calendar_end_date' => '2026-12-15',
            'source_url' => 'https://example.invalid/academic-period-test',
            'source_reference' => 'ACA-YEAR02-PERIOD-FIXTURE',
            'document_sha256' => str_repeat('d', 64),
            'verification_status' => 'draft',
            'validated_at' => null,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $versionId;
    }

    private function periodData(
        string $code,
        string $name,
        int $sequenceNumber,
        string $startDate,
        string $endDate,
        ?string $duration = null,
        ?string $unit = null,
    ): AcademicPeriodData {
        return new AcademicPeriodData(
            code: $code,
            name: $name,
            sequenceNumber: $sequenceNumber,
            startDate: $startDate,
            endDate: $endDate,
            declaredDuration: $duration,
            durationUnit: $unit,
        );
    }
}
