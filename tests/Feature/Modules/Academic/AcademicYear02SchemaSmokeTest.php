<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class AcademicYear02SchemaSmokeTest extends TestCase
{
    private const EXPECTED_DATABASE = 'cenicom_aca_year02_isolated_test';

    private const TABLES = [
        'academic_regulatory_authorities',
        'official_calendar_publications',
        'official_calendar_publication_versions',
        'academic_years',
        'academic_periods',
        'academic_calendar_events',
    ];

    public function test_it_runs_only_against_the_dedicated_isolated_database(): void
    {
        $this->assertSame('testing', app()->environment());

        $row = DB::selectOne('SELECT DATABASE() AS current_database');
        $this->assertSame(self::EXPECTED_DATABASE, $row->current_database ?? null);
    }

    public function test_all_six_aca_year02_tables_exist(): void
    {
        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), sprintf('Falta la tabla %s.', $table));
        }
    }

    public function test_expected_foreign_keys_and_check_constraints_are_present(): void
    {
        $tablePlaceholders = implode(',', array_fill(0, count(self::TABLES), '?'));

        $foreignKeys = DB::select(
            "SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS "
            . "WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_TYPE = 'FOREIGN KEY' "
            . "AND TABLE_NAME IN ({$tablePlaceholders})",
            self::TABLES,
        );

        $checks = DB::select(
            "SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS "
            . "WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_TYPE = 'CHECK' "
            . "AND TABLE_NAME IN ({$tablePlaceholders})",
            self::TABLES,
        );

        $this->assertSame(8, (int) ($foreignKeys[0]->total ?? 0), 'No coincide el número de FK ACA-YEAR-02.');
        $this->assertSame(21, (int) ($checks[0]->total ?? 0), 'No coincide el número de CHECK ACA-YEAR-02.');
    }

    public function test_required_columns_and_indexes_exist(): void
    {
        $this->assertTrue(Schema::hasColumn('official_calendar_publications', 'identity_fingerprint'));
        $this->assertTrue(Schema::hasColumn('official_calendar_publication_versions', 'supersedes_version_id'));
        $this->assertTrue(Schema::hasColumn('academic_years', 'publication_version_id'));
        $this->assertTrue(Schema::hasColumn('academic_periods', 'declared_duration'));
        $this->assertTrue(Schema::hasColumn('academic_calendar_events', 'closure_prerequisite'));

        $indexes = DB::select(
            "SELECT COUNT(*) AS total FROM information_schema.STATISTICS "
            . "WHERE TABLE_SCHEMA = DATABASE() AND INDEX_NAME IN ("
            . "'uq_ocp_identity_fingerprint', 'uq_ocpv_pub_version', 'uq_ay_institution_cycle', "
            . "'uq_apv_version_sequence', 'uq_acev_version_code')"
        );
        $this->assertGreaterThanOrEqual(5, (int) ($indexes[0]->total ?? 0));
    }
}
