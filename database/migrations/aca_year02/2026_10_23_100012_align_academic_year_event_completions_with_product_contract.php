<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AUTHORIZED_DATABASE = 'cenicom_aca_year02_isolated_test';

    public function up(): void
    {
        $this->assertIsolatedDatabase();

        $schema = Schema::connection('mysql');

        foreach ([
            'academic_years',
            'academic_calendar_events',
            'academic_year_event_completions',
        ] as $table) {
            if (! $schema->hasTable($table)) {
                throw new RuntimeException(
                    "ACA-YEAR-02 100012: falta la tabla requerida {$table}; no se aplicaron cambios.",
                );
            }
        }

        if ($schema->hasColumn(
            'academic_year_event_completions',
            'publication_version_id',
        )) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012: publication_version_id ya existe; inspeccionar el esquema antes de continuar.',
            );
        }

        // Validate the existing schema before performing any DDL.
        $yearsIndexExists = $this->assertIndexAbsentOrCompatible(
            'academic_years',
            'uq_ay_version_id',
            ['publication_version_id', 'id'],
        );

        $eventsIndexExists = $this->assertIndexAbsentOrCompatible(
            'academic_calendar_events',
            'uq_acev_version_id',
            ['publication_version_id', 'id'],
        );

        if (
            ! $this->hasForeignKey('fk_ayec_academic_year')
            || ! $this->hasForeignKey('fk_ayec_calendar_event')
        ) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012: no se encontraron las dos claves foráneas históricas esperadas; requiere inspección.',
            );
        }

        if (
            $this->hasForeignKey('fk_ayec_year_version')
            || $this->hasForeignKey('fk_ayec_event_version')
        ) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012: ya existe alguna clave foránea compuesta; requiere inspección.',
            );
        }

        if ($this->hasParentVersionMismatch()) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012: hay hitos cuya versión no coincide con la del año lectivo; no se aplicaron cambios.',
            );
        }

        // These candidate keys are necessary for the composite foreign keys.
        if (! $yearsIndexExists) {
            Schema::connection('mysql')->table(
                'academic_years',
                function (Blueprint $table): void {
                    $table->unique(
                        ['publication_version_id', 'id'],
                        'uq_ay_version_id',
                    );
                },
            );
        }

        if (! $eventsIndexExists) {
            Schema::connection('mysql')->table(
                'academic_calendar_events',
                function (Blueprint $table): void {
                    $table->unique(
                        ['publication_version_id', 'id'],
                        'uq_acev_version_id',
                    );
                },
            );
        }

        // Add nullable first, backfill from the validated parent relationship,
        // and only then make the column mandatory.
        Schema::connection('mysql')->table(
            'academic_year_event_completions',
            function (Blueprint $table): void {
                $table->char('publication_version_id', 26)
                    ->nullable()
                    ->after('id');
            },
        );

        DB::connection('mysql')->statement(<<<'SQL'
            UPDATE `academic_year_event_completions` AS `completion`
            INNER JOIN `academic_years` AS `academic_year`
                ON `academic_year`.`id` = `completion`.`academic_year_id`
            INNER JOIN `academic_calendar_events` AS `event`
                ON `event`.`id` = `completion`.`event_id`
            SET `completion`.`publication_version_id` =
                `academic_year`.`publication_version_id`
            WHERE `completion`.`publication_version_id` IS NULL
              AND `academic_year`.`publication_version_id` =
                  `event`.`publication_version_id`
        SQL);

        if (
            DB::connection('mysql')
                ->table('academic_year_event_completions')
                ->whereNull('publication_version_id')
                ->exists()
            || $this->hasCompletionVersionMismatch()
        ) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012: la conciliación de versiones no quedó completa; inspeccionar antes de continuar.',
            );
        }

        Schema::connection('mysql')->table(
            'academic_year_event_completions',
            function (Blueprint $table): void {
                $table->char('publication_version_id', 26)
                    ->nullable(false)
                    ->change();

                $table->dropForeign('fk_ayec_academic_year');
                $table->dropForeign('fk_ayec_calendar_event');
            },
        );

        Schema::connection('mysql')->table(
            'academic_year_event_completions',
            function (Blueprint $table): void {
                $table->foreign(
                    ['publication_version_id', 'academic_year_id'],
                    'fk_ayec_year_version',
                )
                    ->references(['publication_version_id', 'id'])
                    ->on('academic_years')
                    ->restrictOnDelete()
                    ->restrictOnUpdate();

                $table->foreign(
                    ['publication_version_id', 'event_id'],
                    'fk_ayec_event_version',
                )
                    ->references(['publication_version_id', 'id'])
                    ->on('academic_calendar_events')
                    ->restrictOnDelete()
                    ->restrictOnUpdate();
            },
        );
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        $schema = Schema::connection('mysql');

        if (! $schema->hasTable('academic_year_event_completions')) {
            return;
        }

        if (! $schema->hasColumn(
            'academic_year_event_completions',
            'publication_version_id',
        )) {
            return;
        }

        if (
            ! $this->hasForeignKey('fk_ayec_year_version')
            || ! $this->hasForeignKey('fk_ayec_event_version')
        ) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rollback refused: no se encuentran las claves foráneas compuestas esperadas.',
            );
        }

        if ($this->hasCompletionVersionMismatch()) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rollback refused: hay inconsistencias de versión; preservar los datos.',
            );
        }

        if (
            $this->hasForeignKey('fk_ayec_academic_year')
            || $this->hasForeignKey('fk_ayec_calendar_event')
        ) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rollback refused: existen claves foráneas históricas inesperadas.',
            );
        }

        Schema::connection('mysql')->table(
            'academic_year_event_completions',
            function (Blueprint $table): void {
                $table->dropForeign('fk_ayec_year_version');
                $table->dropForeign('fk_ayec_event_version');
            },
        );

        Schema::connection('mysql')->table(
            'academic_year_event_completions',
            function (Blueprint $table): void {
                $table->foreign('academic_year_id', 'fk_ayec_academic_year')
                    ->references('id')
                    ->on('academic_years')
                    ->restrictOnDelete()
                    ->restrictOnUpdate();

                $table->foreign('event_id', 'fk_ayec_calendar_event')
                    ->references('id')
                    ->on('academic_calendar_events')
                    ->restrictOnDelete()
                    ->restrictOnUpdate();

                $table->dropColumn('publication_version_id');
            },
        );

        // Keep the additional candidate-key indexes. They are harmless and
        // may be useful to other integrity constraints.
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rejected: APP_ENV must be testing.',
            );
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rejected: default connection must be mysql.',
            );
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rejected: configuration cache exists.',
            );
        }

        $config = (array) config('database.connections.mysql', []);

        $expected = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3308',
            'database' => self::AUTHORIZED_DATABASE,
            'username' => 'aca_year02_test',
        ];

        foreach ($expected as $key => $value) {
            $actual = $config[$key] ?? null;

            if ($key === 'port') {
                $actual = $actual === null ? null : (string) $actual;
            }

            if ($actual !== $value) {
                throw new RuntimeException(sprintf(
                    'ACA-YEAR-02 100012 rejected: mysql.%s must be %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException(
                'ACA-YEAR-02 100012 rejected: DB_URL must be empty.',
            );
        }
    }

    /**
     * Return true if the named index already exists with the expected
     * unique definition; return false if it does not exist.
     *
     * @param list<string> $expectedColumns
     */
    private function assertIndexAbsentOrCompatible(
        string $table,
        string $indexName,
        array $expectedColumns,
    ): bool {
        $rows = DB::connection('mysql')
            ->table('information_schema.statistics')
            ->select([
                'COLUMN_NAME as column_name',
                'SEQ_IN_INDEX as sequence_number',
                'NON_UNIQUE as non_unique',
            ])
            ->where('TABLE_SCHEMA', self::AUTHORIZED_DATABASE)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->orderBy('SEQ_IN_INDEX')
            ->get();

        if ($rows->isEmpty()) {
            return false;
        }

        $actualColumns = $rows
            ->map(static fn (object $row): string => (string) $row->column_name)
            ->all();

        if (
            $actualColumns !== $expectedColumns
            || (int) $rows->first()->non_unique !== 0
        ) {
            throw new RuntimeException(sprintf(
                'ACA-YEAR-02 100012: el índice %s.%s existe con una definición inesperada.',
                $table,
                $indexName,
            ));
        }

        return true;
    }

    private function hasForeignKey(string $name): bool
    {
        return DB::connection('mysql')
            ->table('information_schema.table_constraints')
            ->where('CONSTRAINT_SCHEMA', self::AUTHORIZED_DATABASE)
            ->where('TABLE_NAME', 'academic_year_event_completions')
            ->where('CONSTRAINT_NAME', $name)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function hasParentVersionMismatch(): bool
    {
        return DB::connection('mysql')
            ->table('academic_year_event_completions as completion')
            ->leftJoin(
                'academic_years as academic_year',
                'academic_year.id',
                '=',
                'completion.academic_year_id',
            )
            ->leftJoin(
                'academic_calendar_events as event',
                'event.id',
                '=',
                'completion.event_id',
            )
            ->where(function ($query): void {
                $query->whereNull('academic_year.id')
                    ->orWhereNull('event.id')
                    ->orWhereColumn(
                        'academic_year.publication_version_id',
                        '<>',
                        'event.publication_version_id',
                    );
            })
            ->exists();
    }

    private function hasCompletionVersionMismatch(): bool
    {
        return DB::connection('mysql')
            ->table('academic_year_event_completions as completion')
            ->leftJoin(
                'academic_years as academic_year',
                'academic_year.id',
                '=',
                'completion.academic_year_id',
            )
            ->leftJoin(
                'academic_calendar_events as event',
                'event.id',
                '=',
                'completion.event_id',
            )
            ->where(function ($query): void {
                $query->whereNull('academic_year.id')
                    ->orWhereNull('event.id')
                    ->orWhereColumn(
                        'academic_year.publication_version_id',
                        '<>',
                        'event.publication_version_id',
                    )
                    ->orWhereColumn(
                        'completion.publication_version_id',
                        '<>',
                        'academic_year.publication_version_id',
                    )
                    ->orWhereColumn(
                        'completion.publication_version_id',
                        '<>',
                        'event.publication_version_id',
                    );
            })
            ->exists();
    }
};
