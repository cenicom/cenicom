<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AcademicCalendarStructureIntegrityTest extends TestCase
{
    use DatabaseTransactions;

    private const EXPECTED_DATABASE = 'cenicom_aca_year02_isolated_test';

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame('testing', app()->environment());
        $row = DB::selectOne('SELECT DATABASE() AS current_database');
        $this->assertSame(self::EXPECTED_DATABASE, $row->current_database ?? null);
    }

    public function test_accepts_variable_period_counts_gaps_and_adjacent_periods(): void
    {
        $versionId = $this->createAvailableVersion();

        $this->createPeriod($versionId, '2026-01-10', '2026-01-31', 1);
        $this->createPeriod($versionId, '2026-02-10', '2026-03-15', 2); // Gap allowed.
        $this->createPeriod($versionId, '2026-03-16', '2026-05-31', 3); // Adjacent days do not overlap.
        $this->createPeriod($versionId, '2026-07-01', '2026-08-15', 4); // Another gap allowed.

        $this->assertSame(4, DB::table('academic_periods')
            ->where('publication_version_id', $versionId)
            ->count());
    }

    public function test_rejects_period_starting_before_official_window(): void
    {
        $versionId = $this->createAvailableVersion();

        $this->assertF07Rejected(
            fn () => $this->createPeriod($versionId, '2026-01-09', '2026-02-01', 1),
            'ACA-F07: el periodo debe quedar dentro de las fechas oficiales',
        );
    }

    public function test_rejects_period_ending_after_official_window(): void
    {
        $versionId = $this->createAvailableVersion();

        $this->assertF07Rejected(
            fn () => $this->createPeriod($versionId, '2026-11-01', '2026-12-16', 1),
            'ACA-F07: el periodo debe quedar dentro de las fechas oficiales',
        );
    }

    public function test_rejects_periods_that_overlap_including_a_shared_endpoint(): void
    {
        $versionId = $this->createAvailableVersion();
        $this->createPeriod($versionId, '2026-01-10', '2026-02-01', 1);

        $this->assertF07Rejected(
            fn () => $this->createPeriod($versionId, '2026-02-01', '2026-03-01', 2),
            'ACA-F07: los periodos de una misma version oficial no pueden solaparse',
        );
    }

    public function test_rejects_period_update_that_creates_an_overlap(): void
    {
        $versionId = $this->createAvailableVersion();
        $firstPeriodId = $this->createPeriod($versionId, '2026-01-10', '2026-01-31', 1);
        $secondPeriodId = $this->createPeriod($versionId, '2026-02-10', '2026-03-15', 2);

        $this->assertF07Rejected(
            fn () => DB::table('academic_periods')->where('id', $secondPeriodId)->update([
                'start_date' => '2026-01-25',
            ]),
            'ACA-F07: la actualizacion solaparia periodos',
        );

        $this->assertDatabaseHas('academic_periods', [
            'id' => $firstPeriodId,
            'start_date' => '2026-01-10',
            'end_date' => '2026-01-31',
        ], 'mysql');
        $this->assertDatabaseHas('academic_periods', [
            'id' => $secondPeriodId,
            'start_date' => '2026-02-10',
            'end_date' => '2026-03-15',
        ], 'mysql');
    }

    public function test_rejects_period_update_outside_official_window(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-01', 1);

        $this->assertF07Rejected(
            fn () => DB::table('academic_periods')->where('id', $periodId)->update([
                'end_date' => '2026-12-16',
            ]),
            'ACA-F07: el periodo actualizado debe quedar dentro de las fechas oficiales',
        );
    }

    public function test_rejects_period_update_that_would_leave_an_existing_event_outside(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-31', 1);
        $this->createEvent($versionId, '2026-02-10', '2026-02-11', $periodId);

        $this->assertF07Rejected(
            fn () => DB::table('academic_periods')->where('id', $periodId)->update([
                'start_date' => '2026-02-15',
            ]),
            'ACA-F07: el periodo no puede cambiar de version ni de intervalo',
        );
    }

    public function test_rejects_event_outside_official_window_even_without_period(): void
    {
        $versionId = $this->createAvailableVersion();

        $this->assertF07Rejected(
            fn () => $this->createEvent($versionId, '2026-12-15', '2026-12-16'),
            'ACA-F07: el evento debe quedar dentro de las fechas oficiales',
        );
    }

    public function test_allows_general_event_without_period_and_keeps_requirement_flags_distinct(): void
    {
        $versionId = $this->createAvailableVersion();
        $eventId = $this->createEvent(
            $versionId,
            '2026-04-01',
            '2026-04-02',
            null,
            'PEDAGOGICAL_DAY',
            true,
            false,
            false,
        );

        $this->assertDatabaseHas('academic_calendar_events', [
            'id' => $eventId,
            'academic_period_id' => null,
            'is_officially_required' => true,
            'activation_prerequisite' => false,
            'closure_prerequisite' => false,
        ], 'mysql');
    }

    public function test_rejects_event_starting_before_its_period(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-31', 1);

        $this->assertF07Rejected(
            fn () => $this->createEvent($versionId, '2026-01-31', '2026-02-05', $periodId),
            'ACA-F07: el evento debe quedar dentro de su periodo academico',
        );
    }

    public function test_rejects_event_ending_after_its_period(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-31', 1);

        $this->assertF07Rejected(
            fn () => $this->createEvent($versionId, '2026-03-30', '2026-04-01', $periodId),
            'ACA-F07: el evento debe quedar dentro de su periodo academico',
        );
    }

    public function test_allows_exam_interval_contained_in_its_period(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-31', 1);
        $examId = $this->createEvent(
            $versionId,
            '2026-03-10',
            '2026-03-15',
            $periodId,
            'EXAM_INTERVAL',
        );

        $this->assertDatabaseHas('academic_calendar_events', [
            'id' => $examId,
            'event_type' => 'EXAM_INTERVAL',
            'academic_period_id' => $periodId,
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-15',
        ], 'mysql');
    }

    public function test_rejects_exam_interval_outside_its_period(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-31', 1);

        $this->assertF07Rejected(
            fn () => $this->createEvent(
                $versionId,
                '2026-03-30',
                '2026-04-02',
                $periodId,
                'EXAM_INTERVAL',
            ),
            'ACA-F07: el evento debe quedar dentro de su periodo academico',
        );
    }

    public function test_rejects_exam_interval_without_period(): void
    {
        $versionId = $this->createAvailableVersion();

        $this->assertF07Rejected(
            fn () => $this->createEvent($versionId, '2026-04-01', '2026-04-03', null, 'EXAM_INTERVAL'),
            'ACA-F07: un intervalo de examenes debe pertenecer a un periodo academico',
        );
    }

    public function test_rejects_event_update_that_exits_its_period(): void
    {
        $versionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($versionId, '2026-02-01', '2026-03-31', 1);
        $eventId = $this->createEvent($versionId, '2026-02-10', '2026-02-12', $periodId);

        $this->assertF07Rejected(
            fn () => DB::table('academic_calendar_events')->where('id', $eventId)->update([
                'end_date' => '2026-04-01',
            ]),
            'ACA-F07: el evento actualizado debe quedar dentro de su periodo academico',
        );

        $this->assertDatabaseHas('academic_calendar_events', [
            'id' => $eventId,
            'end_date' => '2026-02-12',
        ], 'mysql');
    }

    public function test_rejects_event_update_outside_official_window(): void
    {
        $versionId = $this->createAvailableVersion();
        $eventId = $this->createEvent($versionId, '2026-04-01', '2026-04-02');

        $this->assertF07Rejected(
            fn () => DB::table('academic_calendar_events')->where('id', $eventId)->update([
                'end_date' => '2026-12-16',
            ]),
            'ACA-F07: el evento actualizado debe quedar dentro de las fechas oficiales',
        );
    }

    public function test_rejects_event_linked_to_period_from_another_version(): void
    {
        $firstVersionId = $this->createAvailableVersion();
        $secondVersionId = $this->createAvailableVersion();
        $periodId = $this->createPeriod($firstVersionId, '2026-02-01', '2026-03-31', 1);

        try {
            $this->createEvent($secondVersionId, '2026-02-10', '2026-02-12', $periodId);
            $this->fail('La operación debía rechazar un periodo perteneciente a otra versión.');
        } catch (QueryException $exception) {
            $this->assertStringContainsString(
                'ACA-F07: el evento debe quedar dentro de su periodo academico',
                $exception->getMessage(),
            );
        }
    }

    private function createAvailableVersion(
        string $calendarStart = '2026-01-10',
        string $calendarEnd = '2026-12-15',
    ): string {
        $now = now();
        $authorityId = (string) Str::ulid();
        $publicationId = (string) Str::ulid();
        $versionId = (string) Str::ulid();

        DB::table('academic_regulatory_authorities')->insert([
            'id' => $authorityId,
            'country_code' => 'CO',
            'authority_code' => 'F07-' . substr($authorityId, -8),
            'official_name' => 'Autoridad de prueba ACA-F07',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publications')->insert([
            'id' => $publicationId,
            'authority_id' => $authorityId,
            'document_type' => 'ACADEMIC_CALENDAR',
            'document_number' => 'F07-' . substr($publicationId, -8),
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'GENERAL',
            'region_code' => 'ALL',
            'calendar_code' => 'A',
            'identity_status' => 'confirmed',
            'identity_fingerprint' => hash('sha256', 'ACA-F07:' . $publicationId),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $versionId,
            'publication_id' => $publicationId,
            'version_number' => 1,
            'supersedes_version_id' => null,
            'official_version_label' => 'F07 test version 1',
            'authority_name_snapshot' => 'Autoridad de prueba ACA-F07',
            'title' => 'Calendario escolar de prueba ACA-F07',
            'promulgated_on' => '2025-12-20',
            'calendar_start_date' => $calendarStart,
            'calendar_end_date' => $calendarEnd,
            'source_url' => 'https://example.invalid/aca-f07/' . $versionId,
            'source_reference' => 'ACA-F07-SOURCE-' . substr($versionId, -8),
            'document_sha256' => null,
            'verification_status' => 'available',
            'validated_at' => $now,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $versionId;
    }

    private function createPeriod(
        string $versionId,
        string $startDate,
        string $endDate,
        int $sequence,
    ): string {
        $id = (string) Str::ulid();
        $now = now();

        DB::table('academic_periods')->insert([
            'id' => $id,
            'publication_version_id' => $versionId,
            'code' => 'PERIOD-' . $sequence . '-' . substr($id, -4),
            'name' => 'Período ' . $sequence,
            'sequence_number' => $sequence,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'declared_duration' => null,
            'duration_unit' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }

    private function createEvent(
        string $versionId,
        string $startDate,
        string $endDate,
        ?string $periodId = null,
        string $type = 'OTHER',
        bool $officiallyRequired = false,
        bool $activationPrerequisite = false,
        bool $closurePrerequisite = false,
    ): string {
        $id = (string) Str::ulid();
        $now = now();

        DB::table('academic_calendar_events')->insert([
            'id' => $id,
            'publication_version_id' => $versionId,
            'academic_period_id' => $periodId,
            'code' => 'EVENT-' . substr($id, -8),
            'event_type' => $type,
            'title' => 'Evento de prueba ACA-F07',
            'audience' => 'ALL',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_officially_required' => $officiallyRequired,
            'activation_prerequisite' => $activationPrerequisite,
            'closure_prerequisite' => $closurePrerequisite,
            'source_reference' => 'ACA-F07-EVENT-' . substr($id, -8),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }

    private function assertF07Rejected(callable $operation, string $expectedMessage): void
    {
        try {
            $operation();
            $this->fail('La operación debió ser rechazada por las guardas temporales ACA-F07.');
        } catch (QueryException $exception) {
            $this->assertStringContainsString($expectedMessage, $exception->getMessage());
        }
    }
}
