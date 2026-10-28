<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\Services\CompleteAcademicYearEvent;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mockery;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearHttpCreationTest extends AcademicYear02TestCase
{
    use DatabaseTransactions;

    private string $institutionId;

    private string $publicationVersionId;

    private bool $allowCancellation = true;

    private bool $allowClosure = true;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'array',
        ]);

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $identity = Mockery::mock(IdentityInterface::class);

        $identity->shouldReceive('id')
            ->andReturn('aca-year02-http-test-actor');

        $identity->shouldReceive('name')
            ->andReturn('ACA-YEAR-02 HTTP test actor');

        $identity->shouldReceive('roles')
            ->andReturn([]);

        $identity->shouldReceive('authenticated')
            ->andReturn(true);

        $identity->shouldReceive('can')
            ->with('academic_year.create')
            ->andReturn(true);

        $identity->shouldReceive('can')
            ->with('academic_year.activate')
            ->andReturn(true);

        $identity->shouldReceive('can')
            ->with('academic_year.cancel')
            ->andReturnUsing(
                fn(): bool => $this->allowCancellation,
            );

        $identity->shouldReceive('can')
            ->with('academic_year.close')
            ->andReturnUsing(
                fn(): bool => $this->allowClosure,
            );

        $identity->shouldReceive('can')
            ->with('academic_year.complete_milestone')
            ->andReturn(true);

        $identity->shouldReceive('permissions')
            ->andReturnUsing(function (): array {
                $permissions = [
                    'academic_year.create',
                    'academic_year.activate',
                ];

                if ($this->allowCancellation) {
                    $permissions[] = 'academic_year.cancel';
                }

                if ($this->allowClosure) {
                    $permissions[] = 'academic_year.close';
                    $permissions[] = 'academic_year.complete_milestone';
                }

                return $permissions;
            });

        $this->app->instance(
            IdentityInterface::class,
            $identity,
        );

        $authorization = Mockery::mock(
            AuthorizationServiceInterface::class,
        );

        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.activate')
            ->andReturn(true);

        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.create')
            ->andReturn(true);

        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.cancel')
            ->andReturnUsing(
                fn($candidate, string $permission): bool =>
                    $this->allowCancellation,
            );

        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.close')
            ->andReturnUsing(
                fn($candidate, string $permission): bool =>
                    $this->allowClosure,
            );

        $authorization->shouldReceive('can')
            ->with($identity, 'academic_year.complete_milestone')
            ->andReturn(true);

        $this->app->instance(
            AuthorizationServiceInterface::class,
            $authorization,
        );

        /*
         * Simula únicamente el estado de autenticación requerido
         * por los middleware auth y verified.
         *
         * No crea usuarios ni tablas en la base aislada.
         */
        Route::middleware(['web', 'auth', 'verified'])
            ->get(
                '/__test/aca-year02-authenticated',
                static fn() => response()->noContent(),
            );

        $this->withoutMiddleware(
            \Illuminate\Auth\Middleware\Authenticate::class,
        );

        $this->withoutMiddleware(
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        );

        $this->createInstitutionAndOfficialVersion();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_authorized_request_creates_academic_year_over_http(): void
    {
        $response = $this
            ->from('/academic-years')
            ->post(route('academic-years.store'), [
                'institutionId' => $this->institutionId,
                'publicationVersionId' => $this->publicationVersionId,
                'cycleCode' => '2026',
                'calendarCode' => 'A',
            ]);

        $response
            ->assertRedirect('/academic-years')
            ->assertSessionHas(
                'success',
                fn(string $message): bool =>
                    str_contains(
                        $message,
                        'Año lectivo creado en estado borrador.',
                    ),
            );

        $year = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->where('cycle_code', '2026')
            ->first();

        $this->assertNotNull($year);
        $this->assertSame('draft', $year->status);

        $this->assertSame(
            $this->publicationVersionId,
            $year->publication_version_id,
        );

        $this->assertSame('A', $year->calendar_code);

        $audit = DB::connection('mysql')
            ->table('audit_entries')
            ->where('subject_type', 'academic_year')
            ->where('subject_id', $year->id)
            ->where('action', 'academic_year.created')
            ->first();

        $this->assertNotNull($audit);
        $this->assertSame('success', $audit->result);

        $this->assertSame(
            'aca-year02-http-test-actor',
            $audit->actor_id,
        );
    }

    public function test_authorized_request_activates_academic_year_over_http(): void
    {
        $this->post(route('academic-years.store'), [
            'institutionId' => $this->institutionId,
            'publicationVersionId' => $this->publicationVersionId,
            'cycleCode' => '2027',
            'calendarCode' => 'A',
        ])->assertRedirect();

        $year = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->where('cycle_code', '2027')
            ->first();

        $this->assertNotNull($year);
        $this->assertSame('draft', $year->status);

        $response = $this
            ->from('/academic-years')
            ->post(route('academic-years.activate', [
                'academicYearId' => $year->id,
            ]));

        $response
            ->assertRedirect('/academic-years')
            ->assertSessionHas(
                'success',
                'Año lectivo activado correctamente.',
            );

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'active',
        ], 'mysql');

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'academic_year',
            'subject_id' => $year->id,
            'action' => 'academic_year.activated',
            'result' => 'success',
            'actor_id' => 'aca-year02-http-test-actor',
        ], 'mysql');
    }

    public function test_authorized_request_cancels_draft_academic_year_over_http(): void
    {
        $this->post(route('academic-years.store'), [
            'institutionId' => $this->institutionId,
            'publicationVersionId' => $this->publicationVersionId,
            'cycleCode' => '2028',
            'calendarCode' => 'A',
        ])->assertRedirect();

        $year = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->where('cycle_code', '2028')
            ->first();

        $this->assertNotNull($year);
        $this->assertSame('draft', $year->status);

        $reason = 'Publicación duplicada por error.';

        $response = $this
            ->from('/academic-years')
            ->post(route('academic-years.cancel', [
                'academicYearId' => $year->id,
            ]), [
                'reason' => $reason,
            ]);

        $response
            ->assertRedirect('/academic-years')
            ->assertSessionHas(
                'success',
                'Año lectivo cancelado correctamente.',
            );

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ], 'mysql');

        $this->assertDatabaseHas('audit_entries', [
            'subject_type' => 'academic_year',
            'subject_id' => $year->id,
            'action' => 'academic_year.cancelled',
            'result' => 'success',
            'actor_id' => 'aca-year02-http-test-actor',
        ], 'mysql');
    }

    public function test_request_without_cancel_permission_is_forbidden_and_preserves_draft(): void
    {
        $this->post(route('academic-years.store'), [
            'institutionId' => $this->institutionId,
            'publicationVersionId' => $this->publicationVersionId,
            'cycleCode' => '2029',
            'calendarCode' => 'A',
        ])->assertRedirect();

        $year = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->where('cycle_code', '2029')
            ->first();

        $this->assertNotNull($year);
        $this->assertSame('draft', $year->status);

        $this->allowCancellation = false;

        $response = $this->post(route('academic-years.cancel', [
            'academicYearId' => $year->id,
        ]), [
            'reason' => 'Cancelación de prueba sin permiso.',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'draft',
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::connection('mysql')
                ->table('audit_entries')
                ->where('subject_type', 'academic_year')
                ->where('subject_id', $year->id)
                ->where('action', 'academic_year.cancelled')
                ->count(),
        );
    }

    public function test_authorized_request_closes_academic_year_over_http_and_records_audit(): void
    {
        Carbon::setTestNow('2026-12-17 11:30:00');

        $this->post(route('academic-years.store'), [
            'institutionId' => $this->institutionId,
            'publicationVersionId' => $this->publicationVersionId,
            'cycleCode' => '2030',
            'calendarCode' => 'A',
        ])->assertRedirect();

        $year = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->where('cycle_code', '2030')
            ->first();

        $this->assertNotNull($year);
        $this->assertSame('draft', $year->status);

        $this->post(route('academic-years.activate', [
            'academicYearId' => $year->id,
        ]))->assertRedirect();

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'active',
        ], 'mysql');

        $eventId = (string) Str::ulid();
        $now = now();

        DB::connection('mysql')
            ->table('academic_calendar_events')
            ->insert([
                'id' => $eventId,
                'publication_version_id' => $this->publicationVersionId,
                'academic_period_id' => null,
                'code' => 'FINAL_REPORTS',
                'event_type' => 'INSTITUTIONAL_DEVELOPMENT',
                'title' => 'Hito FINAL_REPORTS',
                'audience' => 'ALL',
                'start_date' => '2026-01-13',
                'end_date' => '2026-01-15',
                'is_officially_required' => true,
                'activation_prerequisite' => false,
                'closure_prerequisite' => true,
                'source_reference' => 'Fixture de prueba ACA-F02',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        app(CompleteAcademicYearEvent::class)->execute(
            (string) $year->id,
            $eventId,
        );

        $this->assertDatabaseHas('academic_year_event_completions', [
            'academic_year_id' => $year->id,
            'event_id' => $eventId,
            'completed_by' => 'aca-year02-http-test-actor',
        ], 'mysql');

        $response = $this
            ->from('/academic-years')
            ->post(route('academic-years.close', [
                'academicYearId' => $year->id,
            ]));

        $response
            ->assertRedirect('/academic-years')
            ->assertSessionHas(
                'success',
                'Año lectivo cerrado correctamente.',
            );

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'closed',
            'closed_at' => '2026-12-17 11:30:00',
        ], 'mysql');

        $this->assertDatabaseHas('audit_entries', [
            'subject_id' => $year->id,
            'action' => 'academic_year.closed',
            'result' => 'success',
            'actor_id' => 'aca-year02-http-test-actor',
        ], 'mysql');
    }

    public function test_request_without_close_permission_is_forbidden_and_preserves_active_year(): void
    {
        $this->post(route('academic-years.store'), [
            'institutionId' => $this->institutionId,
            'publicationVersionId' => $this->publicationVersionId,
            'cycleCode' => '2031',
            'calendarCode' => 'A',
        ])->assertRedirect();

        $year = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->where('cycle_code', '2031')
            ->first();

        $this->assertNotNull($year);
        $this->assertSame('draft', $year->status);

        $this->post(route('academic-years.activate', [
            'academicYearId' => $year->id,
        ]))->assertRedirect();

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'active',
        ], 'mysql');

        $this->allowClosure = false;

        $response = $this->post(route('academic-years.close', [
            'academicYearId' => $year->id,
        ]));

        $response->assertForbidden();

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'status' => 'active',
            'closed_at' => null,
        ], 'mysql');

        $this->assertSame(
            0,
            DB::connection('mysql')
                ->table('audit_entries')
                ->where('subject_id', $year->id)
                ->where('action', 'academic_year.closed')
                ->count(),
        );
    }

    private function createInstitutionAndOfficialVersion(): void
    {
        $now = now();

        $this->institutionId = (string) Str::ulid();

        $authorityId = (string) Str::ulid();
        $authorityCode = 'TEST-' . substr($authorityId, 0, 12);

        $publicationId = (string) Str::ulid();

        $this->publicationVersionId = (string) Str::ulid();

        $shortCode = $this->uniqueInstitutionShortCode();

        DB::connection('mysql')
            ->table('institutions')
            ->insert([
                'id' => $this->institutionId,
                'code' => 'ACA-' . substr($this->institutionId, 0, 12),
                'short_code' => $shortCode,
                'name' => 'Institución de prueba ACA HTTP',
                'official_registration_country' => null,
                'official_registration_authority' => null,
                'official_registration_value' => null,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        DB::connection('mysql')
            ->table('academic_regulatory_authorities')
            ->insert([
                'id' => $authorityId,
                'country_code' => 'CO',
                'authority_code' => $authorityCode,
                'official_name' => 'Autoridad de prueba ACA HTTP',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        DB::connection('mysql')
            ->table('official_calendar_publications')
            ->insert([
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

        DB::connection('mysql')
            ->table('official_calendar_publication_versions')
            ->insert([
                'id' => $this->publicationVersionId,
                'publication_id' => $publicationId,
                'version_number' => 1,
                'supersedes_version_id' => null,
                'official_version_label' => null,
                'authority_name_snapshot' => 'Autoridad de prueba ACA HTTP',
                'title' => 'Calendario de pruebas ACA HTTP',
                'promulgated_on' => '2026-01-01',
                'calendar_start_date' => '2026-01-13',
                'calendar_end_date' => '2026-12-06',
                'source_url' => 'https://example.invalid/aca-http-test',
                'source_reference' => 'Fixture exclusiva de pruebas',
                'document_sha256' => null,
                'verification_status' => 'available',
                'validated_at' => $now,
                'withdrawn_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        DB::connection('mysql')
            ->table('institution_academic_profiles')
            ->insert([
                'institution_id' => $this->institutionId,
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

    private function uniqueInstitutionShortCode(): string
    {
        do {
            $shortCode = strtoupper(
                Str::random(8),
            );
        } while (
            preg_match('/^[A-Z]{6,15}$/', $shortCode) !== 1
            || DB::connection('mysql')
                ->table('institutions')
                ->where('short_code', $shortCode)
                ->exists()
        );

        return $shortCode;
    }
}
