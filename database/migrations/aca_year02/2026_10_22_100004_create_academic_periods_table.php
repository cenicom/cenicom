<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertIsolatedDatabase();

        Schema::create('academic_periods', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('publication_version_id', 26);
            $table->string('code', 20);
            $table->string('name', 100);
            $table->unsignedSmallInteger('sequence_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('declared_duration', 6, 2)->nullable();
            $table->string('duration_unit', 8)->nullable();
            $table->timestamps();

            $table->foreign('publication_version_id', 'fk_apv_publication_version')
                ->references('id')->on('official_calendar_publication_versions')
                ->onDelete('restrict')->onUpdate('restrict');

            $table->unique(['publication_version_id', 'code'], 'uq_apv_version_code');
            $table->unique(['publication_version_id', 'sequence_number'], 'uq_apv_version_sequence');
            $table->unique(['publication_version_id', 'id'], 'uq_apv_version_id');
            $table->index(['publication_version_id', 'start_date', 'end_date'], 'ix_apv_period_dates');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE academic_periods
            ADD CONSTRAINT chk_apv_dates
                CHECK (start_date <= end_date),
            ADD CONSTRAINT chk_apv_sequence
                CHECK (sequence_number > 0),
            ADD CONSTRAINT chk_apv_duration
                CHECK (
                    (declared_duration IS NULL AND duration_unit IS NULL)
                    OR
                    (declared_duration IS NOT NULL AND declared_duration > 0 AND duration_unit IN ('DAYS', 'WEEKS'))
                )
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        Schema::dropIfExists('academic_periods');
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-YEAR-02 rechazado: APP_ENV debe ser testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-YEAR-02 rechazado: la conexión por defecto debe ser mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-YEAR-02 rechazado: existe bootstrap/cache/config.php. No se ejecuta con configuración cacheada.');
        }

        $config = (array) config('database.connections.mysql', []);
        $expected = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3308',
            'database' => 'cenicom_aca_year02_isolated_test',
            'username' => 'aca_year02_test',
        ];

        foreach ($expected as $key => $value) {
            $actual = $config[$key] ?? null;
            if ($key === 'port') {
                $actual = $actual === null ? null : (string) $actual;
            }
            if ($actual !== $value) {
                throw new RuntimeException(sprintf(
                    'ACA-YEAR-02 rechazado: database.connections.mysql.%s debe ser %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-YEAR-02 rechazado: DB_URL debe estar vacío.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-YEAR-02 rechazado: DB_PASSWORD debe estar configurada y no vacía.');
        }

        // Esta conexión solo se abre después de comprobar la configuración apuntada a localhost.
        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        $actualDatabase = $row->current_database ?? null;
        if ($actualDatabase !== 'cenicom_aca_year02_isolated_test') {
            throw new RuntimeException(sprintf(
                'ACA-YEAR-02 rechazado: la base efectiva es %s, no cenicom_aca_year02_isolated_test.',
                var_export($actualDatabase, true),
            ));
        }
    }
};
