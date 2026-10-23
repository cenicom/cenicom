<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\Services\CancelAcademicYear;
use Carbon\Carbon;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearCancellationTest extends AcademicYear02TestCase
{
    use DatabaseTransactions;

    private array $institutionIds = [];
    private array $authorityIds = [];
    private array $publicationIds = [];
    private array $versionIds = [];
    private array $yearIds = [];
    private bool $authenticatedIdentity = true;
    private array $grantedPermissions = ['academic_year.cancel'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $identity = Mockery::mock(IdentityInterface::class);
        $identity->shouldReceive('id')->andReturn('aca-year02-cancel-actor');
        $identity->shouldReceive('name')->andReturn('ACA-YEAR-02 cancellation test actor');
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

    public function test_cancels_draft_preserves_record_and_audits_reason_and_transition(): void
    {
        Carbon::setTestNow('2026-10-22 09:30:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();
        $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-01');

        app(CancelAcademicYear::class)->execute($yearId, '  Publicación duplicada por error.  ');

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'institution_id' => $institutionId,
            'publication_version_id' => $versionId,
            'cycle_code' => 'CANCEL-01',
            'status' => 'cancelled',
            'activated_at' => null,
            'closed_at' => null,
            'cancellation_reason' => 'Publicación duplicada por error.',
            'cancelled_at' => '2026-10-22 09:30:00',
        ], 'mysql');

        $this->assertSame(1, DB::table('academic_years')->where('id', $yearId)->count());

        $audit = DB::table('audit_entries')
            ->where('subject_type', 'academic_year')
            ->where('subject_id', $yearId)
            ->where('action', 'academic_year.cancelled')
            ->first();

        $this->assertNotNull($audit);
        $this->assertSame('success', $audit->result);
        $this->assertSame('aca-year02-cancel-actor', $audit->actor_id);
        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('draft', $metadata['status_before']);
        $this->assertSame('cancelled', $metadata['status_after']);
        $this->assertSame('Publicación duplicada por error.', $metadata['cancellation_reason']);
        $this->assertSame($institutionId, $metadata['institution_id']);
    }

    public function test_rejects_empty_or_whitespace_reason_without_mutation(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();
        $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-02');

        foreach (['', '   ', "\n\t  "] as $reason) {
            $this->assertDomainException(
                fn () => app(CancelAcademicYear::class)->execute($yearId, $reason),
                'es obligatoria',
            );
        }

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'draft',
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ], 'mysql');
        $this->assertSame(0, DB::table('audit_entries')->where('subject_id', $yearId)->count());
    }

    public function test_rejects_reason_longer_than_storage_contract(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();
        $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-03');

        $this->assertDomainException(
            fn () => app(CancelAcademicYear::class)->execute($yearId, str_repeat('x', 1001)),
            '1000 caracteres',
        );

        $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => 'draft'], 'mysql');
    }

    public function test_cannot_cancel_active_closed_or_already_cancelled_year(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();

        foreach (['active', 'closed', 'cancelled'] as $status) {
            $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-' . strtoupper($status), $status);

            $this->assertDomainException(
                fn () => app(CancelAcademicYear::class)->execute($yearId, 'Razón válida.'),
                'solo se puede cancelar',
            );

            $this->assertDatabaseHas('academic_years', ['id' => $yearId, 'status' => $status], 'mysql');
        }
    }

    public function test_nonexistent_year_is_rejected(): void
    {
        $this->assertDomainException(
            fn () => app(CancelAcademicYear::class)->execute((string) Str::ulid(), 'Razón válida.'),
            'no existe',
        );
    }

    public function test_requires_cancel_permission(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();
        $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-NO-PERM');
        $this->grantedPermissions = [];

        $this->expectException(AuthorizationException::class);
        app(CancelAcademicYear::class)->execute($yearId, 'Razón válida.');
    }

    public function test_requires_authenticated_identity(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();
        $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-NO-AUTH');
        $this->authenticatedIdentity = false;

        $this->expectException(AuthorizationException::class);
        app(CancelAcademicYear::class)->execute($yearId, 'Razón válida.');
    }

    public function test_audit_failure_rolls_back_cancellation(): void
    {
        [$institutionId, $versionId] = $this->createInstitutionAndVersion();
        $yearId = $this->createYear($institutionId, $versionId, 'CANCEL-AUDIT-FAIL');

        $recorder = Mockery::mock(AuditRecorderInterface::class);
        $recorder->shouldReceive('record')->once()->andThrow(new RuntimeException('audit fixture failure'));
        $this->app->instance(AuditRecorderInterface::class, $recorder);

        try {
            app(CancelAcademicYear::class)->execute($yearId, 'Razón válida.');
            $this->fail('El fallo de auditoría debía revertir la cancelación.');
        } catch (RuntimeException $exception) {
            $this->assertSame('audit fixture failure', $exception->getMessage());
        }

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearId,
            'status' => 'draft',
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ], 'mysql');
    }

    public function test_cancel_permission_is_registered_by_academic_definition(): void
    {
        $registry = app(\App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class);
        $permission = $registry->permission('academic_year.cancel');
        $this->assertNotNull($permission);
        $this->assertSame('academic_year.cancel', $permission->key());
    }

    private function createInstitutionAndVersion(): array
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
            'name' => 'Institución de prueba ACA-F03',
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
            'official_name' => 'Autoridad de prueba ACA-F03',
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
            'authority_name_snapshot' => 'Autoridad de prueba ACA-F03',
            'title' => 'Calendario de pruebas ACA-F03',
            'promulgated_on' => '2026-01-01',
            'calendar_start_date' => '2026-01-01',
            'calendar_end_date' => '2026-12-31',
            'source_url' => 'https://example.invalid/aca-year02-f03-test',
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
        $cancelled = $status === 'cancelled';

        DB::table('academic_years')->insert([
            'id' => $id,
            'institution_id' => $institutionId,
            'publication_version_id' => $versionId,
            'cycle_code' => $cycleCode,
            'calendar_code' => 'A',
            'status' => $status,
            'activated_at' => in_array($status, ['active', 'closed'], true) ? $now : null,
            'closed_at' => $status === 'closed' ? $now : null,
            'cancelled_at' => $cancelled ? $now : null,
            'cancellation_reason' => $cancelled ? 'Cancelación de fixture.' : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->yearIds[] = $id;
        return $id;
    }

    private function assertDomainException(callable $operation, string $messageFragment): void
    {
        try {
            $operation();
            $this->fail('Se esperaba DomainException.');
        } catch (DomainException $exception) {
            $this->assertStringContainsString(mb_strtolower($messageFragment), mb_strtolower($exception->getMessage()));
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
