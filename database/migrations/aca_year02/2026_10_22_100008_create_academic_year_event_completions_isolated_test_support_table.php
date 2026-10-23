<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ACA-F02 schema proposal: records per-year completion of official calendar events.
 * Guarded to the isolated development database; do not deploy this guarded file as-is.
 */
return new class extends Migration
{
    private const AUTHORIZED_DATABASE = 'cenicom_aca_year02_isolated_test';

    public function up(): void
    {
        $this->assertIsolatedDatabase();

        if (Schema::connection('mysql')->hasTable('academic_year_event_completions')) {
            throw new RuntimeException(
                'ACA-F02 migration refused: academic_year_event_completions already exists; inspect its provenance.',
            );
        }

        Schema::connection('mysql')->create('academic_year_event_completions', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->char('id', 26)->primary();
            $table->char('academic_year_id', 26);
            $table->char('event_id', 26);
            $table->string('completed_by', 100);
            $table->dateTime('completed_at');
            $table->timestamps();

            $table->foreign('academic_year_id', 'fk_ayec_academic_year')
                ->references('id')->on('academic_years')
                ->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('event_id', 'fk_ayec_calendar_event')
                ->references('id')->on('academic_calendar_events')
                ->restrictOnDelete()->restrictOnUpdate();

            $table->unique(
                ['academic_year_id', 'event_id'],
                'uq_ayec_year_event',
            );
            $table->index(['academic_year_id', 'completed_at'], 'ix_ayec_year_completed_at');
            $table->comment('ACA-F02: completion records in isolated schema; verify/promote schema before deployment.');
        });
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        if (! Schema::connection('mysql')->hasTable('academic_year_event_completions')) {
            return;
        }

        if (DB::connection('mysql')->table('academic_year_event_completions')->exists()) {
            throw new RuntimeException(
                'ACA-F02 rollback refused: completion history exists; preserve records and inspect explicitly.',
            );
        }

        Schema::connection('mysql')->drop('academic_year_event_completions');
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-F02 migration rejected: APP_ENV must be testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-F02 migration rejected: default connection must be mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-F02 migration rejected: configuration cache exists.');
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
                    'ACA-F02 migration rejected: mysql.%s must be %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-F02 migration rejected: DB_URL must be empty.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-F02 migration rejected: DB_PASSWORD must be non-empty.');
        }

        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        if (($row->current_database ?? null) !== self::AUTHORIZED_DATABASE) {
            throw new RuntimeException('ACA-F02 migration rejected: effective database is not authorized.');
        }
    }
};
