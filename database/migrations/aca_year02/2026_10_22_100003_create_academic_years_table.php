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

        Schema::create('academic_years', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('institution_id', 26);
            $table->char('publication_version_id', 26);
            $table->string('cycle_code', 20);
            $table->char('calendar_code', 1);
            $table->string('status', 12)->default('draft');
            $table->dateTime('activated_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason', 1000)->nullable();
            $table->timestamps();

            $table->foreign('institution_id', 'fk_ay_institution')
                ->references('id')->on('institutions')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('publication_version_id', 'fk_ay_publication_version')
                ->references('id')->on('official_calendar_publication_versions')
                ->onDelete('restrict')->onUpdate('restrict');

            $table->unique(['institution_id', 'cycle_code'], 'uq_ay_institution_cycle');
            $table->index(['institution_id', 'status'], 'ix_ay_institution_status');
            $table->index(['publication_version_id', 'status'], 'ix_ay_version_status');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE academic_years
            ADD CONSTRAINT chk_ay_calendar_code
                CHECK (calendar_code IN ('A', 'B')),
            ADD CONSTRAINT chk_ay_cycle_code
                CHECK (CHAR_LENGTH(TRIM(cycle_code)) > 0),
            ADD CONSTRAINT chk_ay_status_lifecycle
                CHECK (
                    (status = 'draft'
                        AND activated_at IS NULL AND closed_at IS NULL AND cancelled_at IS NULL AND cancellation_reason IS NULL)
                    OR
                    (status = 'active'
                        AND activated_at IS NOT NULL AND closed_at IS NULL AND cancelled_at IS NULL AND cancellation_reason IS NULL)
                    OR
                    (status = 'closed'
                        AND activated_at IS NOT NULL AND closed_at IS NOT NULL AND cancelled_at IS NULL AND cancellation_reason IS NULL)
                    OR
                    (status = 'cancelled'
                        AND activated_at IS NULL AND closed_at IS NULL AND cancelled_at IS NOT NULL
                        AND cancellation_reason IS NOT NULL AND CHAR_LENGTH(TRIM(cancellation_reason)) > 0)
                )
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        Schema::dropIfExists('academic_years');
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
