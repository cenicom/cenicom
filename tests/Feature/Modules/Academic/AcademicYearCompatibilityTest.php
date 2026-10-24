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

    private function insertDraftOfficialCalendarVersion(
        bool $withDates = true,
        bool $withSourceReference = true,
        bool $withSourceLocator = true,
    ): string
    {
        $id = (string) Str::ulid();
        $now = now();

        $latestNumber = DB::table('official_calendar_publication_versions')
            ->where('publication_id', $this->publicationId)
            ->max('version_number');

        $authorityName = DB::table('academic_regulatory_authorities')
            ->where('id', $this->authorityId)
            ->value('official_name');

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $id,
            'publication_id' => $this->publicationId,
            'version_number' => ((int) $latestNumber) + 1,
            'supersedes_version_id' => $this->versionId,
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
