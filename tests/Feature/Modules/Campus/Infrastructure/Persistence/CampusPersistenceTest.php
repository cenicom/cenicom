<?php

namespace Tests\Feature\Modules\Campus\Infraestructure\Persistence;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CampusPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_campus_tables_exist_in_sqlite(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->assertTrue(Schema::hasTable('institutions'));
        $this->assertTrue(Schema::hasTable('addresses'));
        $this->assertTrue(Schema::hasTable('campus_code_sequences'));
        $this->assertTrue(Schema::hasTable('campuses'));
    }

    public function test_campuses_has_expected_columns_and_nullability(): void
    {
        $expectedColumns = [
            'id',
            'institution_id',
            'code',
            'short_code',
            'name',
            'ministry_code',
            'address_id',
            'status',
            'created_at',
            'updated_at',
        ];

        $actualColumns = Schema::getColumnListing('campuses');

        sort($expectedColumns);
        sort($actualColumns);

        $this->assertSame($expectedColumns, $actualColumns);

        $this->assertColumnNullable('campuses', 'ministry_code', true);
        $this->assertColumnNullable('campuses', 'id', false);
        $this->assertColumnNullable('campuses', 'institution_id', false);
        $this->assertColumnNullable('campuses', 'code', false);
        $this->assertColumnNullable('campuses', 'short_code', false);
        $this->assertColumnNullable('campuses', 'name', false);
        $this->assertColumnNullable('campuses', 'address_id', false);
        $this->assertColumnNullable('campuses', 'status', false);
        $this->assertColumnNullable('campuses', 'created_at', true);
        $this->assertColumnNullable('campuses', 'updated_at', true);
    }

    public function test_campus_code_sequences_has_expected_columns_and_nullability(): void
    {
        $expectedColumns = [
            'institution_id',
            'current_value',
            'created_at',
            'updated_at',
        ];

        $actualColumns = Schema::getColumnListing('campus_code_sequences');

        sort($expectedColumns);
        sort($actualColumns);

        $this->assertSame($expectedColumns, $actualColumns);

        $this->assertColumnNullable('campus_code_sequences', 'institution_id', false);
        $this->assertColumnNullable('campus_code_sequences', 'current_value', false);
        $this->assertColumnNullable('campus_code_sequences', 'created_at', true);
        $this->assertColumnNullable('campus_code_sequences', 'updated_at', true);
    }

    public function test_campus_tables_have_expected_primary_and_unique_indexes(): void
    {
        $campusIndexes = Schema::getIndexes('campuses');
        $sequenceIndexes = Schema::getIndexes('campus_code_sequences');

        $this->assertIndexExists($campusIndexes, ['id'], true, true);
        $this->assertIndexExists($campusIndexes, ['code'], true, false);
        $this->assertIndexExists(
            $campusIndexes,
            ['institution_id', 'short_code'],
            true,
            false
        );

        $this->assertIndexExists(
            $sequenceIndexes,
            ['institution_id'],
            true,
            true
        );
    }

    public function test_campus_tables_have_expected_foreign_keys(): void
    {
        $campusForeignKeys = Schema::getForeignKeys('campuses');
        $sequenceForeignKeys = Schema::getForeignKeys('campus_code_sequences');

        $this->assertForeignKeyExists(
            $campusForeignKeys,
            ['institution_id'],
            'institutions',
            ['id']
        );

        $this->assertForeignKeyExists(
            $campusForeignKeys,
            ['address_id'],
            'addresses',
            ['id']
        );

        $this->assertForeignKeyExists(
            $sequenceForeignKeys,
            ['institution_id'],
            'institutions',
            ['id']
        );
    }

    public function test_campus_tables_have_expected_defaults(): void
    {
        $this->assertColumnDefault('campuses', 'status', 'draft');
        $this->assertColumnDefault('campus_code_sequences', 'current_value', '0');
    }

    private function assertColumnNullable(
        string $table,
        string $columnName,
        bool $expected
    ): void {
        $column = collect(Schema::getColumns($table))
            ->firstWhere('name', $columnName);

        $this->assertNotNull(
            $column,
            "La columna {$table}.{$columnName} debe existir."
        );

        $this->assertSame(
            $expected,
            (bool) $column['nullable'],
            "Nulabilidad inesperada para {$table}.{$columnName}."
        );
    }

    private function assertColumnDefault(
        string $table,
        string $columnName,
        string $expected
    ): void {
        $column = collect(Schema::getColumns($table))
            ->firstWhere('name', $columnName);

        $this->assertNotNull(
            $column,
            "La columna {$table}.{$columnName} debe existir."
        );

        $actual = trim((string) $column['default'], "'\"");

        $this->assertSame(
            $expected,
            $actual,
            "Valor predeterminado inesperado para {$table}.{$columnName}."
        );
    }

    private function assertIndexExists(
        array $indexes,
        array $expectedColumns,
        bool $unique,
        bool $primary
    ): void {
        $found = collect($indexes)->contains(
            function (array $index) use ($expectedColumns, $unique, $primary): bool {
                return $index['columns'] === $expectedColumns
                    && (bool) $index['unique'] === $unique
                    && (bool) $index['primary'] === $primary;
            }
        );

        $this->assertTrue(
            $found,
            sprintf(
                'No se encontró el índice esperado: columnas [%s], unique=%s, primary=%s.',
                implode(', ', $expectedColumns),
                $unique ? 'true' : 'false',
                $primary ? 'true' : 'false'
            )
        );
    }

    private function assertForeignKeyExists(
        array $foreignKeys,
        array $columns,
        string $foreignTable,
        array $foreignColumns
    ): void {
        $found = collect($foreignKeys)->contains(
            function (array $foreignKey) use (
                $columns,
                $foreignTable,
                $foreignColumns
            ): bool {
                return $foreignKey['columns'] === $columns
                    && $foreignKey['foreign_table'] === $foreignTable
                    && $foreignKey['foreign_columns'] === $foreignColumns;
            }
        );

        $this->assertTrue(
            $found,
            sprintf(
                'No se encontró la FK [%s] → %s [%s].',
                implode(', ', $columns),
                $foreignTable,
                implode(', ', $foreignColumns)
            )
        );
    }
}
