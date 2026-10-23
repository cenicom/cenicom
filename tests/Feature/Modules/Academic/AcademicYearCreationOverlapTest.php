<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicYearCreateData;
use App\Modules\Academic\Domain\Services\AcademicYearCreator;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearCreationOverlapTest extends AcademicYear02TestCase
{
    use DatabaseTransactions;

    /** @var list<string> */
    private array $institutionIds = [];

    /** @var list<string> */
    private array $authorityIds = [];

    /** @var list<string> */
    private array $publicationIds = [];

    /** @var list<string> */
    private array $versionIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $identity = Mockery::mock(IdentityInterface::class);
        $identity->shouldReceive('id')->andReturn('aca-year02-test-actor');
        $identity->shouldReceive('name')->andReturn('ACA-YEAR-02 test actor');
        $identity->shouldReceive('authenticated')->andReturn(true);
        $this->app->instance(IdentityInterface::class, $identity);

        $authorization = Mockery::mock(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
        );
        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.create')
            ->andReturn(true);
        $this->app->instance(
            \App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface::class,
            $authorization,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_creates_draft_and_audits_official_interval(): void
    {
        $institutionId = $this->createInstitution();
        $versionId = $this->createAvailableVersion('2026-01-13', '2026-12-06');

        $id = $this->creator()->execute(new AcademicYearCreateData(
            institutionId: $institutionId,
            publicationVersionId: $versionId,
            cycleCode: '2026',
            calendarCode: 'A',
        ));

        $this->assertDatabaseHas('academic_years', [
            'id' => $id,
            'institution_id' => $institutionId,
            'publication_version_id' => $versionId,
            'cycle_code' => '2026',
            'calendar_code' => 'A',
            'status' => 'draft',
        ], 'mysql');

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'academic_year')
            ->where('subject_id', $id)
            ->where('action', 'academic_year.created')
            ->first();

        $this->assertNotNull($audit);
        $this->assertSame('success', $audit->result);
        $this->assertSame('aca-year02-test-actor', $audit->actor_id);

        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('2026-01-13', $metadata['calendar_start_date']);
        $this->assertSame('2026-12-06', $metadata['calendar_end_date']);
    }

    public function test_rejects_overlapping_interval_for_same_institution(): void
    {
        $institutionId = $this->createInstitution();
        $firstVersion = $this->createAvailableVersion('2026-01-01', '2026-12-31');
        $overlappingVersion = $this->createAvailableVersion('2026-06-01', '2027-05-31');

        $this->createYear($institutionId, $firstVersion, '2026');

        $this->assertDomainException(
            fn () => $this->createYear($institutionId, $overlappingVersion, '2026-2027'),
            'se solapa',
        );

        $this->assertSame(
            1,
            DB::table('academic_years')->where('institution_id', $institutionId)->count(),
        );
    }

    public function test_treats_shared_endpoint_as_overlap(): void
    {
        $institutionId = $this->createInstitution();
        $firstVersion = $this->createAvailableVersion('2026-01-01', '2026-12-31');
        $boundaryVersion = $this->createAvailableVersion('2026-12-31', '2027-12-30');

        $this->createYear($institutionId, $firstVersion, '2026');

        $this->assertDomainException(
            fn () => $this->createYear($institutionId, $boundaryVersion, '2027'),
            'se solapa',
        );
    }

    public function test_allows_adjacent_non_overlapping_intervals(): void
    {
        $institutionId = $this->createInstitution();
        $firstVersion = $this->createAvailableVersion('2026-01-01', '2026-12-31');
        $nextVersion = $this->createAvailableVersion('2027-01-01', '2027-12-31');

        $first = $this->createYear($institutionId, $firstVersion, '2026');
        $second = $this->createYear($institutionId, $nextVersion, '2027');

        $this->assertNotSame($first, $second);
        $this->assertSame(
            2,
            DB::table('academic_years')->where('institution_id', $institutionId)->count(),
        );
    }

    public function test_allows_identical_intervals_for_different_institutions(): void
    {
        $firstInstitution = $this->createInstitution();
        $secondInstitution = $this->createInstitution();
        $version = $this->createAvailableVersion('2026-01-01', '2026-12-31');

        $first = $this->createYear($firstInstitution, $version, '2026');
        $second = $this->createYear($secondInstitution, $version, '2026');

        $this->assertNotSame($first, $second);
        $this->assertSame(
            2,
            DB::table('academic_years')
                ->whereIn('institution_id', [$firstInstitution, $secondInstitution])
                ->count(),
        );
    }

    public function test_active_and_closed_years_block_overlapping_calendars(): void
    {
        foreach (['active', 'closed'] as $status) {
            $institutionId = $this->createInstitution();
            $existingVersion = $this->createAvailableVersion('2026-01-01', '2026-12-31');
            $overlappingVersion = $this->createAvailableVersion('2026-12-01', '2027-11-30');

            $this->insertYearWithStatus(
                institutionId: $institutionId,
                versionId: $existingVersion,
                cycleCode: 'HIST-' . strtoupper($status),
                status: $status,
            );

            $this->assertDomainException(
                fn () => $this->createYear($institutionId, $overlappingVersion, 'NEXT-' . strtoupper($status)),
                'se solapa',
            );
        }
    }

    public function test_cancelled_year_does_not_block_an_overlapping_calendar(): void
    {
        $institutionId = $this->createInstitution();
        $version = $this->createAvailableVersion('2026-01-01', '2026-12-31');
        $this->insertCancelledYear($institutionId, $version, 'CANCELLED-2026');

        $newId = $this->createYear($institutionId, $version, '2026');

        $this->assertDatabaseHas('academic_years', [
            'id' => $newId,
            'status' => 'draft',
        ], 'mysql');
        $this->assertSame(
            2,
            DB::table('academic_years')->where('institution_id', $institutionId)->count(),
        );
    }

    public function test_cycle_code_is_not_reusable_after_cancellation(): void
    {
        $institutionId = $this->createInstitution();
        $version = $this->createAvailableVersion('2026-01-01', '2026-12-31');
        $this->insertCancelledYear($institutionId, $version, '2026');

        $this->assertDomainException(
            fn () => $this->createYear($institutionId, $version, '2026'),
            'código de ciclo',
        );
    }

    public function test_rejects_version_that_is_not_available(): void
    {
        $institutionId = $this->createInstitution();
        $versionId = $this->createAvailableVersion('2026-01-01', '2026-12-31');
        DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->update(['verification_status' => 'validated']);

        $this->assertDomainException(
            fn () => $this->createYear($institutionId, $versionId, '2026'),
            'debe estar disponible',
        );
    }

    public function test_rejects_publication_with_unresolved_identity(): void
    {
        $institutionId = $this->createInstitution();
        $versionId = $this->createAvailableVersion(
            '2026-01-01',
            '2026-12-31',
            'A',
            'unresolved',
        );

        $this->assertDomainException(
            fn () => $this->createYear($institutionId, $versionId, '2026'),
            'identidad confirmada',
        );
    }

    public function test_rolls_back_year_when_audit_write_fails(): void
    {
        $institutionId = $this->createInstitution();
        $versionId = $this->createAvailableVersion('2026-01-01', '2026-12-31');

        $recorder = Mockery::mock(AuditRecorderInterface::class);
        $recorder->shouldReceive('record')
            ->once()
            ->andThrow(new RuntimeException('audit fixture failure'));
        $this->app->instance(AuditRecorderInterface::class, $recorder);

        try {
            $this->createYear($institutionId, $versionId, '2026');
            $this->fail('La excepción del auditor debía revertir la creación.');
        } catch (RuntimeException $exception) {
            $this->assertSame('audit fixture failure', $exception->getMessage());
        }

        $this->assertSame(
            0,
            DB::table('academic_years')->where('institution_id', $institutionId)->count(),
        );
    }

    private function creator(): AcademicYearCreator
    {
        return app(AcademicYearCreator::class);
    }

    private function createYear(
        string $institutionId,
        string $versionId,
        string $cycleCode,
        string $calendarCode = 'A',
    ): string {
        return $this->creator()->execute(new AcademicYearCreateData(
            institutionId: $institutionId,
            publicationVersionId: $versionId,
            cycleCode: $cycleCode,
            calendarCode: $calendarCode,
        ));
    }

    private function createInstitution(): string
    {
        $id = (string) Str::ulid();
        $shortCode = $this->uniqueLetters(8);
        $now = now();

        DB::table('institutions')->insert([
            'id' => $id,
            'code' => 'ACA-' . substr($id, 0, 12),
            'short_code' => $shortCode,
            'name' => 'Institución de prueba ACA-F01',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->institutionIds[] = $id;

        return $id;
    }

    private function createAvailableVersion(
        string $startDate,
        string $endDate,
        string $calendarCode = 'A',
        string $identityStatus = 'confirmed',
    ): string {
        $publicationId = (string) Str::ulid();
        $versionId = (string) Str::ulid();
        $now = now();

        $existingProfile = $this->institutionIds === []
            ? null
            : DB::table('institution_academic_profiles')
                ->whereIn('institution_id', $this->institutionIds)
                ->first();

        if ($existingProfile !== null) {
            $authorityId = (string) DB::table('academic_regulatory_authorities')
                ->where('country_code', $existingProfile->country_code)
                ->where('authority_code', $existingProfile->authority_code)
                ->value('id');
            $authorityCode = (string) $existingProfile->authority_code;
        } else {
            $authorityId = (string) Str::ulid();
            $authorityCode = 'TEST-' . substr($authorityId, 0, 12);
            DB::table('academic_regulatory_authorities')->insert([
                'id' => $authorityId,
                'country_code' => 'CO',
                'authority_code' => $authorityCode,
                'official_name' => 'Autoridad de prueba ACA-F01',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

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
            'calendar_code' => $calendarCode,
            'identity_status' => $identityStatus,
            'identity_fingerprint' => $identityStatus === 'confirmed'
                ? hash('sha256', $publicationId)
                : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $versionId,
            'publication_id' => $publicationId,
            'version_number' => 1,
            'supersedes_version_id' => null,
            'official_version_label' => null,
            'authority_name_snapshot' => 'Autoridad de prueba ACA-F01',
            'title' => 'Calendario de pruebas ACA-F01',
            'promulgated_on' => '2026-01-01',
            'calendar_start_date' => $startDate,
            'calendar_end_date' => $endDate,
            'source_url' => 'https://example.invalid/aca-year02-test',
            'source_reference' => 'Fixture exclusiva de pruebas',
            'document_sha256' => null,
            'verification_status' => 'available',
            'validated_at' => $now,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->authorityIds[] = $authorityId;
        $this->publicationIds[] = $publicationId;
        $this->versionIds[] = $versionId;

        foreach ($this->institutionIds as $institutionId) {
            $hasProfile = DB::table('institution_academic_profiles')
                ->where('institution_id', $institutionId)
                ->exists();
            if (! $hasProfile) {
                DB::table('institution_academic_profiles')->insert([
                    'institution_id' => $institutionId,
                    'country_code' => 'CO',
                    'authority_code' => $authorityCode,
                    'jurisdiction_code' => 'ACA_TEST',
                    'regime_code' => 'GENERAL',
                    'region_code' => 'TEST_REGION',
                    'calendar_code' => 'A',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        return $versionId;
    }

    private function insertCancelledYear(
        string $institutionId,
        string $versionId,
        string $cycleCode,
    ): void {
        $this->insertYearWithStatus(
            institutionId: $institutionId,
            versionId: $versionId,
            cycleCode: $cycleCode,
            status: 'cancelled',
        );
    }

    private function insertYearWithStatus(
        string $institutionId,
        string $versionId,
        string $cycleCode,
        string $status,
    ): void {
        $now = now();
        $activatedAt = in_array($status, ['active', 'closed'], true) ? $now : null;
        $closedAt = $status === 'closed' ? $now : null;
        $cancelledAt = $status === 'cancelled' ? $now : null;
        $reason = $status === 'cancelled'
            ? 'Cancelación de fixture de prueba.'
            : null;

        DB::table('academic_years')->insert([
            'id' => (string) Str::ulid(),
            'institution_id' => $institutionId,
            'publication_version_id' => $versionId,
            'cycle_code' => $cycleCode,
            'calendar_code' => 'A',
            'status' => $status,
            'activated_at' => $activatedAt,
            'closed_at' => $closedAt,
            'cancelled_at' => $cancelledAt,
            'cancellation_reason' => $reason,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
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
