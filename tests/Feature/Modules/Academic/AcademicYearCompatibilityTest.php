<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicYearCreateData;
use App\Modules\Academic\Domain\Services\AcademicYearCreator;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearCompatibilityTest extends AcademicYear02TestCase
{
    private string $institutionId;
    private string $authorityId;
    private string $publicationId;
    private string $versionId;
    private string $authorityCode = 'MINEDUC';
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
        $identity->shouldReceive('id')->andReturn('aca-year02-compatibility-actor');
        $identity->shouldReceive('name')->andReturn('ACA-YEAR-02 compatibility test actor');
        $identity->shouldReceive('authenticated')->andReturn(true);
        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.create')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'institution_academic_profile.configure')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.validate')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.publish')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.withdraw')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.correct')
            ->andReturn(true);
        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_publication.confirm_identity')
            ->andReturn(true);
        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );


        $this->seedFixture();
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

    public function test_create_permission_is_registered_by_academic_definition(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $this->assertNotNull($registry->permission('academic_year.create'));
    }

    public function test_creation_requires_create_permission_before_writing_year_or_audit(): void
    {
        $identity = app(IdentityInterface::class);
        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.create')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );

        try {
            $this->createYear('A');
            $this->fail('La creación debía rechazarse sin el permiso academic_year.create.');
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            // Expected: the year and success audit must remain absent.
        }

        $this->assertSame(
            0,
            DB::table('academic_years')
                ->where('institution_id', $this->institutionId)
                ->count(),
        );

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'academic_year')
                ->where('action', 'academic_year.created')
                ->count(),
        );
    }

    public function test_institution_academic_profile_permission_is_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $permission = $registry->permission('institution_academic_profile.configure');

        $this->assertNotNull($permission);
        $this->assertSame('institution_academic_profile.configure', $permission->key());
    }


    public function test_official_calendar_version_correction_permission_is_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $permission = $registry->permission('official_calendar_version.correct');

        $this->assertNotNull($permission);
        $this->assertSame(
            'official_calendar_version.correct',
            $permission->key(),
        );
    }

    public function test_corrects_available_version_with_immutable_successor_and_audit(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        app(
            \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        app(
            \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        $previous = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->first();

        $this->assertNotNull($previous);
        $this->assertSame('available', $previous->verification_status);

        $oldValues = [
            'official_version_label' => $previous->official_version_label,
            'authority_name_snapshot' => $previous->authority_name_snapshot,
            'title' => $previous->title,
            'promulgated_on' => $previous->promulgated_on,
            'calendar_start_date' => $previous->calendar_start_date,
            'calendar_end_date' => $previous->calendar_end_date,
            'source_url' => $previous->source_url,
            'source_reference' => $previous->source_reference,
            'document_sha256' => $previous->document_sha256,
        ];

        $data = new \App\Modules\Academic\Domain\DTO\OfficialCalendarPublicationVersionCorrectionData(
            authorityNameSnapshot: (string) $previous->authority_name_snapshot,
            title: 'Calendario académico oficial corregido',
            officialVersionLabel: 'Corrección de prueba',
            promulgatedOn: '2026-01-12',
            calendarStartDate: '2026-01-12',
            calendarEndDate: '2026-12-20',
            sourceUrl: 'https://example.invalid/aca-year02-correction',
            sourceReference: 'ACA-YEAR02-CORRECTION-001',
            documentSha256: str_repeat('b', 64),
        );

        $newVersionId = app(
            \App\Modules\Academic\Domain\Services\CorrectOfficialCalendarPublicationVersion::class,
        )->execute($versionId, $data);

        $this->assertNotSame($versionId, $newVersionId);

        $previousAfter = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->first();

        $this->assertNotNull($previousAfter);

        $this->assertSame($oldValues, [
            'official_version_label' => $previousAfter->official_version_label,
            'authority_name_snapshot' => $previousAfter->authority_name_snapshot,
            'title' => $previousAfter->title,
            'promulgated_on' => $previousAfter->promulgated_on,
            'calendar_start_date' => $previousAfter->calendar_start_date,
            'calendar_end_date' => $previousAfter->calendar_end_date,
            'source_url' => $previousAfter->source_url,
            'source_reference' => $previousAfter->source_reference,
            'document_sha256' => $previousAfter->document_sha256,
        ]);

        $this->assertSame('available', $previousAfter->verification_status);

        $newValues = $data->toContentArray();

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $newVersionId,
            'publication_id' => $previous->publication_id,
            'version_number' => (int) $previous->version_number + 1,
            'supersedes_version_id' => $versionId,
            'title' => 'Calendario académico oficial corregido',
            'verification_status' => 'draft',
            'validated_at' => null,
            'withdrawn_at' => null,
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'official_calendar_publication_version')
            ->where('subject_id', $newVersionId)
            ->where('action', 'official_calendar_version.corrected')
            ->where('result', 'success')
            ->first();

        $this->assertNotNull($audit);

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame($versionId, $metadata['previous_version_id']);
        $this->assertSame($newVersionId, $metadata['new_version_id']);
        $this->assertSame(
            (int) $previous->version_number,
            $metadata['previous_version_number'],
        );
        $this->assertSame(
            (int) $previous->version_number + 1,
            $metadata['new_version_number'],
        );
        $this->assertSame('available', $metadata['status_before']);
        $this->assertSame('draft', $metadata['status_after']);
        $this->assertSame($oldValues, $metadata['old_values']);
        $this->assertSame($newValues, $metadata['new_values']);
    }

    public function test_correction_rejects_draft_without_creating_version_or_success_audit(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        $version = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->first();

        $this->assertNotNull($version);

        $versionsBefore = DB::table('official_calendar_publication_versions')
            ->where('publication_id', $version->publication_id)
            ->count();

        $auditsBefore = DB::table('audit_entries')
            ->where('action', 'official_calendar_version.corrected')
            ->count();

        $data = new \App\Modules\Academic\Domain\DTO\OfficialCalendarPublicationVersionCorrectionData(
            authorityNameSnapshot: (string) $version->authority_name_snapshot,
            title: 'No debe crearse esta corrección',
            officialVersionLabel: 'Corrección rechazada',
            promulgatedOn: '2026-01-12',
            calendarStartDate: '2026-01-12',
            calendarEndDate: '2026-12-20',
            sourceUrl: 'https://example.invalid/aca-year02-rejected',
            sourceReference: 'ACA-YEAR02-CORRECTION-REJECTED',
            documentSha256: str_repeat('c', 64),
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\CorrectOfficialCalendarPublicationVersion::class,
            )->execute($versionId, $data);

            $this->fail('No debe permitirse corregir una versión en draft.');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString(
                'previamente validada',
                $exception->getMessage(),
            );
        }

        $this->assertSame(
            $versionsBefore,
            DB::table('official_calendar_publication_versions')
                ->where('publication_id', $version->publication_id)
                ->count(),
        );

        $this->assertSame(
            $auditsBefore,
            DB::table('audit_entries')
                ->where('action', 'official_calendar_version.corrected')
                ->count(),
        );

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');
    }

    public function test_configures_academic_profile_and_records_audit(): void
    {
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->delete();

        $profile = new \App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile(
            countryCode: 'CO',
            authorityCode: $this->authorityCode,
            jurisdictionCode: 'NATIONAL',
            regimeCode: 'GENERAL',
            regionCode: 'HUILA',
            calendarCode: 'A',
        );

        app(\App\Modules\Academic\Domain\Services\ConfigureInstitutionAcademicProfile::class)
            ->execute($this->institutionId, $profile);

        $this->assertDatabaseHas('institution_academic_profiles', [
            'institution_id' => $this->institutionId,
            'country_code' => 'CO',
            'authority_code' => $this->authorityCode,
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'GENERAL',
            'region_code' => 'HUILA',
            'calendar_code' => 'A',
        ], 'mysql');

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'institution_academic_profile',
            'subject_id' => $this->institutionId,
            'action' => 'institution_academic_profile.configured',
            'result' => 'success',
        ], 'mysql');
    }

    public function test_profile_configuration_requires_permission(): void
    {
        $identity = app(IdentityInterface::class);
        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'institution_academic_profile.configure')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );

        $before = DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->first();

        try {
            app(\App\Modules\Academic\Domain\Services\ConfigureInstitutionAcademicProfile::class)
                ->execute(
                    $this->institutionId,
                    new \App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile(
                        countryCode: 'CO',
                        authorityCode: $this->authorityCode,
                        jurisdictionCode: 'OTHER',
                        regimeCode: 'GENERAL',
                        regionCode: 'OTHER',
                        calendarCode: 'B',
                    ),
                );
            $this->fail('Se debía rechazar la configuración sin permiso.');
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            // Expected.
        }

        $after = DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->first();

        $this->assertNotNull($before);
        $this->assertNotNull($after);
        $this->assertSame($before->calendar_code, $after->calendar_code);
        $this->assertSame($before->region_code, $after->region_code);

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'institution_academic_profile')
                ->where('subject_id', $this->institutionId)
                ->count(),
        );
    }

    public function test_calendar_switch_is_rejected_when_active_year_exists(): void
    {
        $yearId = $this->createYear('A');

        DB::table('academic_years')
            ->where('id', $yearId)
            ->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

        try {
            app(\App\Modules\Academic\Domain\Services\ConfigureInstitutionAcademicProfile::class)
                ->execute(
                    $this->institutionId,
                    new \App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile(
                        countryCode: 'CO',
                        authorityCode: $this->authorityCode,
                        jurisdictionCode: 'OTHER',
                        regimeCode: 'GENERAL',
                        regionCode: 'HUILA',
                        calendarCode: 'B',
                    ),
                );
            $this->fail('El cambio A/B debía rechazarse con un año activo.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'años lectivos activos o cerrados',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('institution_academic_profiles', [
            'institution_id' => $this->institutionId,
            'calendar_code' => 'A',
            'region_code' => 'NATIONAL',
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'institution_academic_profile')
                ->where('subject_id', $this->institutionId)
                ->where('action', 'institution_academic_profile.updated')
                ->count(),
        );
    }
    public function test_calendar_switch_is_rejected_when_closed_year_exists(): void
    {
        $yearId = $this->createYear('A');
        $closedAt = now();

        DB::table('academic_years')
            ->where('id', $yearId)
            ->update([
                'status' => 'closed',
                'activated_at' => $closedAt,
                'closed_at' => $closedAt,
            ]);

        try {
            app(\App\Modules\Academic\Domain\Services\ConfigureInstitutionAcademicProfile::class)
                ->execute(
                    $this->institutionId,
                    new \App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile(
                        countryCode: 'CO',
                        authorityCode: $this->authorityCode,
                        jurisdictionCode: 'OTHER',
                        regimeCode: 'GENERAL',
                        regionCode: 'NATIONAL',
                        calendarCode: 'B',
                    ),
                );

            $this->fail('El cambio A/B debía rechazarse con un año lectivo cerrado.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'años lectivos activos o cerrados',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'closed',
        ], 'mysql');

        $this->assertDatabaseHas('institution_academic_profiles', [
            'institution_id' => $this->institutionId,
            'calendar_code' => 'A',
            'region_code' => 'NATIONAL',
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'institution_academic_profile')
                ->where('subject_id', $this->institutionId)
                ->where('action', 'institution_academic_profile.updated')
                ->count(),
        );
    }

    public function test_calendar_switch_is_allowed_when_only_draft_year_exists_and_records_update_audit(): void
    {
        $yearId = $this->createYear('A');

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'draft',
        ], 'mysql');

        $this->assertCalendarSwitchAllowedForStatus($yearId, 'draft');
    }

    public function test_calendar_switch_is_allowed_when_only_cancelled_year_exists_and_records_update_audit(): void
    {
        $yearId = $this->createYear('A');
        $cancelledAt = now();

        DB::table('academic_years')
            ->where('id', $yearId)
            ->update([
                'status' => 'cancelled',
                'activated_at' => null,
                'closed_at' => null,
                'cancelled_at' => $cancelledAt,
                'cancellation_reason' => 'Cancelación de prueba ACA-F09.',
            ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'cancelled',
        ], 'mysql');

        $this->assertCalendarSwitchAllowedForStatus($yearId, 'cancelled');
    }

    /**
     * Comprueba el cambio A → B, la conservación del año existente
     * y la auditoría con los valores anteriores y nuevos.
     */
    private function assertCalendarSwitchAllowedForStatus(
        string $yearId,
        string $expectedStatus,
    ): void {
        app(
            \App\Modules\Academic\Domain\Services\ConfigureInstitutionAcademicProfile::class,
        )->execute(
            $this->institutionId,
            new \App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile(
                countryCode: 'CO',
                authorityCode: $this->authorityCode,
                jurisdictionCode: 'OTHER',
                regimeCode: 'GENERAL',
                regionCode: 'HUILA',
                calendarCode: 'B',
            ),
        );

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => $expectedStatus,
        ], 'mysql');

        $this->assertDatabaseHas('institution_academic_profiles', [
            'institution_id' => $this->institutionId,
            'calendar_code' => 'B',
            'jurisdiction_code' => 'OTHER',
            'region_code' => 'HUILA',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'institution_academic_profile')
            ->where('subject_id', $this->institutionId)
            ->where('action', 'institution_academic_profile.updated')
            ->where('result', 'success')
            ->first();

        $this->assertNotNull($audit);

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame(
            'A',
            $metadata['profile_before']['calendar_code'] ?? null,
        );
        $this->assertSame(
            'B',
            $metadata['profile_after']['calendar_code'] ?? null,
        );
    }

    public function test_publication_identity_confirmation_permission_is_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $permission = $registry->permission(
            'official_calendar_publication.confirm_identity',
        );

        $this->assertNotNull($permission);
        $this->assertSame(
            'official_calendar_publication.confirm_identity',
            $permission->key(),
        );
    }

    public function test_confirms_publication_identity_and_records_audit(): void
    {
        $publicationId = $this->insertUnresolvedPublication();

        app(
            \App\Modules\Academic\Domain\Services\ConfirmOfficialCalendarPublicationIdentity::class,
        )->execute($publicationId);

        $publication = DB::table('official_calendar_publications')
            ->where('id', $publicationId)
            ->first();

        $this->assertSame('confirmed', $publication->identity_status);
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{64}$/D',
            (string) $publication->identity_fingerprint,
        );

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'official_calendar_publication',
            'subject_id' => $publicationId,
            'action' => 'official_calendar_publication.identity_confirmed',
            'result' => 'success',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'official_calendar_publication')
            ->where('subject_id', $publicationId)
            ->where('action', 'official_calendar_publication.identity_confirmed')
            ->first();

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame('unresolved', $metadata['identity_status_before']);
        $this->assertSame('confirmed', $metadata['identity_status_after']);
        $this->assertSame(
            $publication->identity_fingerprint,
            $metadata['identity_fingerprint'],
        );
    }

    public function test_publication_identity_confirmation_requires_permission(): void
    {
        $publicationId = $this->insertUnresolvedPublication();
        $identity = app(IdentityInterface::class);

        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_publication.confirm_identity')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\ConfirmOfficialCalendarPublicationIdentity::class,
            )->execute($publicationId);

            $this->fail('La confirmación debía rechazarse sin permiso.');
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            // Expected: no identity update or success audit.
        }

        $this->assertDatabaseHas('official_calendar_publications', [
            'id' => $publicationId,
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication')
                ->where('subject_id', $publicationId)
                ->where('action', 'official_calendar_publication.identity_confirmed')
                ->count(),
        );
    }

    public function test_rejects_duplicate_canonical_publication_identity(): void
    {
        $firstId = $this->insertUnresolvedPublication();
        $secondId = $this->insertUnresolvedPublication();

        $service = app(
            \App\Modules\Academic\Domain\Services\ConfirmOfficialCalendarPublicationIdentity::class,
        );

        $service->execute($firstId);

        try {
            $service->execute($secondId);
            $this->fail('No se debía confirmar una identidad documental duplicada.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'misma identidad canónica',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publications', [
            'id' => $secondId,
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication')
                ->where('subject_id', $secondId)
                ->where('action', 'official_calendar_publication.identity_confirmed')
                ->count(),
        );
    }

    public function test_rejects_reconfirmation_of_already_confirmed_identity(): void
    {
        $publicationId = $this->insertUnresolvedPublication();

        $service = app(
            \App\Modules\Academic\Domain\Services\ConfirmOfficialCalendarPublicationIdentity::class,
        );

        $service->execute($publicationId);

        try {
            $service->execute($publicationId);
            $this->fail('Una publicación confirmada no se debe confirmar de nuevo.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'identidad unresolved',
                $exception->getMessage(),
            );
        }

        $this->assertSame(
            1,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication')
                ->where('subject_id', $publicationId)
                ->where('action', 'official_calendar_publication.identity_confirmed')
                ->count(),
        );
    }

    public function test_rejects_publication_without_calendar_a_or_b(): void
    {
        $publicationId = $this->insertUnresolvedPublication(
            calendarCode: null,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\ConfirmOfficialCalendarPublicationIdentity::class,
            )->execute($publicationId);

            $this->fail('No se debía confirmar una identidad sin calendario A/B.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'calendario A o B',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publications', [
            'id' => $publicationId,
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
        ], 'mysql');
    }

    public function test_rejects_publishing_validated_version_without_source_evidence(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion(
            withSourceLocator: false,
        );

        // Simula un registro heredado validated que no pasó por el servicio actual.
        $at = now();
        $affected = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->where('verification_status', 'draft')
            ->update([
                'verification_status' => 'validated',
                'validated_at' => $at,
                'updated_at' => $at,
            ]);

        $this->assertSame(1, $affected);

        try {
            app(
                \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail(
                'No se debía publicar una versión sin URL oficial ni huella SHA-256.',
            );
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'URL oficial o huella SHA-256',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'validated',
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.published')
                ->count(),
        );
    }

    public function test_rejects_publishing_validated_version_with_unresolved_identity(): void
    {
        $publicationId = $this->insertUnresolvedPublication(
            documentNumber: 'ACA-YEAR02-UNRESOLVED-PUBLISH',
        );

        $versionId = $this->insertDraftOfficialCalendarVersion(
            publicationId: $publicationId,
        );

        // Simula un registro heredado que quedó validated antes de resolver su identidad.
        $at = now();
        $affected = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->where('verification_status', 'draft')
            ->update([
                'verification_status' => 'validated',
                'validated_at' => $at,
                'updated_at' => $at,
            ]);

        $this->assertSame(1, $affected);

        try {
            app(
                \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail(
                'No se debía publicar una versión con identidad unresolved.',
            );
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'identidad confirmada y calendario A/B resuelto',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'validated',
        ], 'mysql');

        $this->assertDatabaseHas('official_calendar_publications', [
            'id' => $publicationId,
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.published')
                ->count(),
        );
    }

    public function test_rejects_republishing_an_available_official_calendar_version(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        app(
            \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        $publisher = app(
            \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
        );

        $publisher->execute($versionId);

        try {
            $publisher->execute($versionId);

            $this->fail(
                'Una versión available no se debe volver a publicar.',
            );
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'estado validated',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'available',
        ], 'mysql');

        $this->assertSame(
            1,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.published')
                ->count(),
            'El intento repetido no debe registrar una segunda publicación exitosa.',
        );
    }

    public function test_official_calendar_version_withdraw_permission_is_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $permission = $registry->permission('official_calendar_version.withdraw');

        $this->assertNotNull($permission);
        $this->assertSame('official_calendar_version.withdraw', $permission->key());
    }

    public function test_creation_rejects_official_calendar_version_after_withdrawal(): void
    {
        app(
            \App\Modules\Academic\Domain\Services\WithdrawOfficialCalendarPublicationVersion::class,
        )->execute($this->versionId);

        $version = DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->first();

        $this->assertNotNull($version);
        $this->assertSame('withdrawn', $version->verification_status);
        $this->assertNotNull($version->withdrawn_at);

        try {
            $this->createYear('A');

            $this->fail(
                'La creación debía rechazarse después de retirar la versión oficial.',
            );
        } catch (DomainException) {
            // Expected: una versión retirada no puede respaldar un año nuevo.
        }

        $this->assertSame(
            0,
            DB::table('academic_years')
                ->where('institution_id', $this->institutionId)
                ->count(),
            'El rechazo no debe dejar creado un año académico.',
        );

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'academic_year')
                ->where('action', 'academic_year.created')
                ->count(),
            'El rechazo no debe registrar una auditoría de creación exitosa.',
        );
    }

    public function test_withdraws_available_official_calendar_version_and_records_audit(): void
    {
        $before = DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->first();

        app(
            \App\Modules\Academic\Domain\Services\WithdrawOfficialCalendarPublicationVersion::class,
        )->execute($this->versionId);

        $after = DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->first();

        $this->assertSame('withdrawn', $after->verification_status);
        $this->assertNotNull($after->withdrawn_at);

        foreach (
            [
                'publication_id',
                'version_number',
                'calendar_start_date',
                'calendar_end_date',
                'source_url',
                'source_reference',
                'document_sha256',
                'validated_at',
                'created_at',
            ] as $field
        ) {
            $this->assertSame(
                $before->{$field},
                $after->{$field},
                "El retiro no debe modificar el contenido histórico: {$field}.",
            );
        }

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'official_calendar_publication_version',
            'subject_id' => $this->versionId,
            'action' => 'official_calendar_version.withdrawn',
            'result' => 'success',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'official_calendar_publication_version')
            ->where('subject_id', $this->versionId)
            ->where('action', 'official_calendar_version.withdrawn')
            ->first();

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame('available', $metadata['status_before']);
        $this->assertSame('withdrawn', $metadata['status_after']);
    }

    public function test_withdraws_validated_official_calendar_version(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        app(
            \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        app(
            \App\Modules\Academic\Domain\Services\WithdrawOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'withdrawn',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'official_calendar_publication_version')
            ->where('subject_id', $versionId)
            ->where('action', 'official_calendar_version.withdrawn')
            ->first();

        $this->assertNotNull($audit);

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame('validated', $metadata['status_before']);
        $this->assertSame('withdrawn', $metadata['status_after']);
    }

    public function test_official_calendar_version_withdrawal_requires_permission(): void
    {
        $identity = app(IdentityInterface::class);

        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.withdraw')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\WithdrawOfficialCalendarPublicationVersion::class,
            )->execute($this->versionId);

            $this->fail('El retiro debía rechazarse sin permiso.');
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            // Expected: the version remains available and no withdrawal audit is written.
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'verification_status' => 'available',
            'withdrawn_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $this->versionId)
                ->where('action', 'official_calendar_version.withdrawn')
                ->count(),
        );
    }

    public function test_rejects_withdrawing_draft_official_calendar_version(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        try {
            app(
                \App\Modules\Academic\Domain\Services\WithdrawOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('No se debía retirar una versión que todavía está en draft.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'estado validated o available',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'withdrawn_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.withdrawn')
                ->count(),
        );
    }

    public function test_rejects_rewithdrawing_a_withdrawn_official_calendar_version(): void
    {
        $service = app(
            \App\Modules\Academic\Domain\Services\WithdrawOfficialCalendarPublicationVersion::class,
        );

        $service->execute($this->versionId);

        $withdrawnAt = DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->value('withdrawn_at');

        try {
            $service->execute($this->versionId);

            $this->fail('No se debía retirar dos veces la misma versión.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'estado validated o available',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'verification_status' => 'withdrawn',
            'withdrawn_at' => $withdrawnAt,
        ], 'mysql');

        $this->assertSame(
            1,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $this->versionId)
                ->where('action', 'official_calendar_version.withdrawn')
                ->count(),
            'El intento repetido no debe generar una segunda auditoría de retiro.',
        );
    }

    public function test_official_calendar_version_publish_permission_is_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $permission = $registry->permission('official_calendar_version.publish');

        $this->assertNotNull($permission);
        $this->assertSame('official_calendar_version.publish', $permission->key());
    }

    public function test_publishes_validated_official_calendar_version_and_records_audit(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        app(
            \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        app(
            \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        $version = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->first();

        $this->assertSame('available', $version->verification_status);
        $this->assertNotNull($version->validated_at);
        $this->assertNull($version->withdrawn_at);

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'official_calendar_publication_version',
            'subject_id' => $versionId,
            'action' => 'official_calendar_version.published',
            'result' => 'success',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'official_calendar_publication_version')
            ->where('subject_id', $versionId)
            ->where('action', 'official_calendar_version.published')
            ->first();

        $this->assertNotNull($audit);

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame('validated', $metadata['status_before']);
        $this->assertSame('available', $metadata['status_after']);
        $this->assertSame('confirmed', $metadata['publication_identity_status']);
        $this->assertSame('A', $metadata['publication_calendar_code']);
        $this->assertSame('url', $metadata['source_locator_type']);
    }

    public function test_publication_version_publish_requires_permission(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        app(
            \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        $identity = app(IdentityInterface::class);
        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.publish')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('La publicación debía rechazarse sin permiso.');
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            // Expected: the version remains validated and no publish audit is written.
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'validated',
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.published')
                ->count(),
        );
    }

    public function test_rejects_publishing_official_calendar_version_that_is_still_draft(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        try {
            app(
                \App\Modules\Academic\Domain\Services\PublishOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('No se debía publicar una versión que todavía está en draft.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'estado validated',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.published')
                ->count(),
        );
    }

    public function test_official_calendar_version_validation_permission_is_registered(): void
    {
        $registry = app(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class,
        );

        $permission = $registry->permission('official_calendar_version.validate');

        $this->assertNotNull($permission);
        $this->assertSame('official_calendar_version.validate', $permission->key());
    }

    public function test_validates_draft_official_calendar_version_and_records_audit(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();

        app(
            \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
        )->execute($versionId);

        $version = DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->first();

        $this->assertNotNull($version);
        $this->assertSame('validated', $version->verification_status);
        $this->assertNotNull($version->validated_at);
        $this->assertNull($version->withdrawn_at);

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'official_calendar_publication_version',
            'subject_id' => $versionId,
            'action' => 'official_calendar_version.validated',
            'result' => 'success',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'official_calendar_publication_version')
            ->where('subject_id', $versionId)
            ->where('action', 'official_calendar_version.validated')
            ->first();

        $this->assertNotNull($audit);

        $metadata = json_decode(
            (string) $audit->metadata,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame('draft', $metadata['status_before']);
        $this->assertSame('validated', $metadata['status_after']);
        $this->assertSame('2026-01-10', $metadata['calendar_start_date']);
        $this->assertSame('2026-12-15', $metadata['calendar_end_date']);
    }

    public function test_version_validation_rejects_unresolved_publication_identity(): void
    {
        $publicationId = $this->insertUnresolvedPublication();
        $versionId = $this->insertDraftOfficialCalendarVersion(
            publicationId: $publicationId,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail(
                'No se debía validar una versión cuya publicación tiene identidad unresolved.',
            );
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'identidad confirmada y calendario A/B resuelto',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.validated')
                ->count(),
            'El rechazo no debe generar auditoría de validación exitosa.',
        );
    }

    public function test_version_validation_requires_permission(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion();
        $identity = app(IdentityInterface::class);

        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'official_calendar_version.validate')
            ->once()
            ->andReturn(false);

        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('La validación debía rechazarse sin permiso.');
        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            // Expected: no state transition or success audit.
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.validated')
                ->count(),
        );
    }

    public function test_version_validation_rejects_missing_official_dates(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion(withDates: false);

        try {
            app(
                \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('No se debía validar una versión sin fechas oficiales.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'fechas oficiales de inicio y fin válidas',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.validated')
                ->count(),
        );
    }
    public function test_version_validation_rejects_missing_source_reference(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion(
            withSourceReference: false,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('La validación debía exigir una referencia documental.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'referencia documental',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.validated')
                ->count(),
        );
    }

    public function test_version_validation_rejects_missing_source_url_and_document_hash(): void
    {
        $versionId = $this->insertDraftOfficialCalendarVersion(
            withSourceLocator: false,
        );

        try {
            app(
                \App\Modules\Academic\Domain\Services\ValidateOfficialCalendarPublicationVersion::class,
            )->execute($versionId);

            $this->fail('La validación debía exigir URL oficial o huella SHA-256.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(
                'URL oficial o una huella SHA-256',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $versionId,
            'verification_status' => 'draft',
            'validated_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'official_calendar_publication_version')
                ->where('subject_id', $versionId)
                ->where('action', 'official_calendar_version.validated')
                ->count(),
        );
    }

    public function test_accepts_exact_compatibility_and_persists_institution_calendar(): void
    {
        $id = $this->createYear('A');

        $this->assertDatabaseHas('academic_years', [
            'id' => $id,
            'institution_id' => $this->institutionId,
            'publication_version_id' => $this->versionId,
            'calendar_code' => 'A',
            'status' => 'draft',
        ], 'mysql');

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'academic_year',
            'subject_id' => $id,
            'action' => 'academic_year.created',
            'result' => 'success',
        ], 'mysql');
    }

    public function test_rejects_country_mismatch(): void
    {
        $this->insertAuthority('EC', $this->authorityCode);
        DB::table('institutions')
            ->where('id', $this->institutionId)
            ->update(['official_registration_country' => null]);
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->update(['country_code' => 'EC']);

        $this->assertCreationRejected('no coincide país');
    }

    public function test_rejects_authority_mismatch(): void
    {
        $this->insertAuthority('CO', 'OTHER_AUTHORITY');
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->update(['authority_code' => 'OTHER_AUTHORITY']);

        $this->assertCreationRejected('no coincide autoridad reguladora');
    }

    public function test_rejects_jurisdiction_mismatch(): void
    {
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->update(['jurisdiction_code' => 'OTHER_JURISDICTION']);

        $this->assertCreationRejected('no coincide jurisdicción');
    }

    public function test_rejects_regime_mismatch(): void
    {
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->update(['regime_code' => 'SPECIAL']);

        $this->assertCreationRejected('no coincide régimen educativo');
    }

    public function test_rejects_region_mismatch(): void
    {
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->update(['region_code' => 'OTHER_REGION']);

        $this->assertCreationRejected('no coincide región');
    }

    public function test_rejects_calendar_mismatch(): void
    {
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->update(['calendar_code' => 'B']);

        $this->assertCreationRejected('calendario A/B de la publicación oficial');
    }

    public function test_rejects_when_caller_requests_calendar_different_from_institution(): void
    {
        $this->assertCreationRejected('calendario A/B solicitado', 'B');
    }

    public function test_rejects_publication_without_explicit_calendar(): void
    {
        DB::table('official_calendar_publications')
            ->where('id', $this->publicationId)
            ->update(['calendar_code' => null]);

        $this->assertCreationRejected('no define calendario A/B');
    }

    public function test_rejects_institution_without_academic_profile(): void
    {
        DB::table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->delete();

        $this->assertCreationRejected('debe tener un perfil académico canónico');
    }

    public function test_rejects_profile_country_that_conflicts_with_official_institution_record(): void
    {
        DB::table('institutions')
            ->where('id', $this->institutionId)
            ->update(['official_registration_country' => 'EC']);

        $this->assertCreationRejected('no coincide con el país del registro oficial');
    }

    private function assertCreationRejected(string $messageFragment, string $requestedCalendar = 'A'): void
    {
        try {
            $this->createYear($requestedCalendar);
            $this->fail('La creación debió rechazarse por incompatibilidad ACA-F05.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString($messageFragment, $exception->getMessage());
        }

        $this->assertSame(
            0,
            DB::table('academic_years')
                ->where('institution_id', $this->institutionId)
                ->count(),
            'El rechazo no debe dejar creado un año académico.',
        );
        $this->assertSame(
            0,
            DB::table('audit_entries')
                ->where('subject_type', 'academic_year')
                ->where('action', 'academic_year.created')
                ->count(),
            'El rechazo no debe generar auditoría de éxito.',
        );
    }

    private function createYear(string $calendarCode): string
    {
        return app(AcademicYearCreator::class)->execute(new AcademicYearCreateData(
            institutionId: $this->institutionId,
            publicationVersionId: $this->versionId,
            cycleCode: '2026',
            calendarCode: $calendarCode,
        ));
    }

    private function insertUnresolvedPublication(
        string $documentNumber = 'ACA-YEAR02-IDENTITY-TEST',
        ?string $calendarCode = 'A',
    ): string {
        $id = (string) Str::ulid();
        $now = now();

        DB::table('official_calendar_publications')->insert([
            'id' => $id,
            'authority_id' => $this->authorityId,
            'document_type' => 'resolution',
            'document_number' => $documentNumber,
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'REGULAR',
            'region_code' => 'NATIONAL',
            'calendar_code' => $calendarCode,
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }

    private function insertDraftOfficialCalendarVersion(
        bool $withDates = true,
        bool $withSourceReference = true,
        bool $withSourceLocator = true,
        ?string $publicationId = null,
    ): string {
        $id = (string) Str::ulid();
        $now = now();
        $targetPublicationId = $publicationId ?? $this->publicationId;

        $latestNumber = DB::table('official_calendar_publication_versions')
            ->where('publication_id', $targetPublicationId)
            ->max('version_number');

        $authorityName = DB::table('academic_regulatory_authorities')
            ->where('id', $this->authorityId)
            ->value('official_name');

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $id,
            'publication_id' => $targetPublicationId,
            'version_number' => ((int) $latestNumber) + 1,
            'supersedes_version_id' => $targetPublicationId === $this->publicationId
                ? $this->versionId
                : null,
            'official_version_label' => 'Prueba de validación',
            'authority_name_snapshot' => (string) $authorityName,
            'title' => 'Versión de prueba para validar un calendario oficial',
            'promulgated_on' => null,
            'calendar_start_date' => $withDates ? '2026-01-10' : null,
            'calendar_end_date' => $withDates ? '2026-12-15' : null,
            'source_url' => $withSourceLocator
                ? 'https://example.invalid/aca-year02-publication'
                : null,
            'source_reference' => $withSourceReference
                ? 'Referencia documental oficial de prueba'
                : null,
            'document_sha256' => null,
            'verification_status' => 'draft',
            'validated_at' => null,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }
    private function seedFixture(): void
    {
        $now = now();
        $this->institutionId = (string) Str::ulid();
        $this->authorityId = (string) Str::ulid();
        $this->publicationId = (string) Str::ulid();
        $this->versionId = (string) Str::ulid();

        DB::table('institutions')->insert([
            'id' => $this->institutionId,
            'code' => 'ACA-' . substr($this->institutionId, 0, 12),
            'name' => 'Institución de prueba ACA-F05',
            'short_code' => 'ACAFIVETEST',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'MINEDUC',
            'official_registration_value' => 'ACA-F05-REG-' . substr($this->institutionId, 0, 8),
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insertAuthority('CO', $this->authorityCode, $this->authorityId);

        DB::table('institution_academic_profiles')->insert([
            'institution_id' => $this->institutionId,
            'country_code' => 'CO',
            'authority_code' => $this->authorityCode,
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'REGULAR',
            'region_code' => 'NATIONAL',
            'calendar_code' => 'A',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publications')->insert([
            'id' => $this->publicationId,
            'authority_id' => $this->authorityId,
            'document_type' => 'resolution',
            'document_number' => 'ACA-F05-' . $this->publicationId,
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'REGULAR',
            'region_code' => 'NATIONAL',
            'calendar_code' => 'A',
            'identity_status' => 'confirmed',
            'identity_fingerprint' => hash('sha256', $this->publicationId),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $this->versionId,
            'publication_id' => $this->publicationId,
            'version_number' => 1,
            'supersedes_version_id' => null,
            'official_version_label' => '2026',
            'authority_name_snapshot' => 'Autoridad reguladora de prueba ACA-F05',
            'title' => 'Calendario académico oficial de prueba ACA-F05',
            'promulgated_on' => '2025-12-15',
            'calendar_start_date' => '2026-01-15',
            'calendar_end_date' => '2026-11-20',
            'source_url' => 'https://example.invalid/aca-f05-calendar',
            'source_reference' => 'Referencia oficial exclusiva de prueba ACA-F05',
            'document_sha256' => null,
            'verification_status' => 'available',
            'validated_at' => $now,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function insertAuthority(string $countryCode, string $authorityCode, ?string $id = null): string
    {
        $id ??= (string) Str::ulid();
        $now = now();

        DB::table('academic_regulatory_authorities')->insert([
            'id' => $id,
            'country_code' => $countryCode,
            'authority_code' => $authorityCode,
            'official_name' => 'Autoridad de prueba ' . $countryCode . ' ' . $authorityCode,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }
}
