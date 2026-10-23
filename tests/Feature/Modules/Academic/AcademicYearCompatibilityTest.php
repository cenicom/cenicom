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
