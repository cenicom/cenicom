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

        Schema::create('academic_calendar_events', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('publication_version_id', 26);
            $table->char('academic_period_id', 26)->nullable();
            $table->string('code', 40);
            $table->string('event_type', 40);
            $table->string('title', 180);
            $table->string('audience', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_officially_required')->default(false);
            $table->boolean('activation_prerequisite')->default(false);
            $table->boolean('closure_prerequisite')->default(false);
            $table->string('source_reference', 200)->nullable();
            $table->timestamps();

            $table->foreign('publication_version_id', 'fk_acev_publication_version')
                ->references('id')->on('official_calendar_publication_versions')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign(['publication_version_id', 'academic_period_id'], 'fk_acev_period_version')
                ->references(['publication_version_id', 'id'])->on('academic_periods')
                ->onDelete('restrict')->onUpdate('restrict');

            $table->unique(['publication_version_id', 'code'], 'uq_acev_version_code');
            $table->index(['publication_version_id', 'event_type', 'start_date', 'end_date'], 'ix_acev_version_type_dates');
            $table->index('academic_period_id', 'ix_acev_period');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE academic_calendar_events
            ADD CONSTRAINT chk_acev_dates
                CHECK (start_date <= end_date),
            ADD CONSTRAINT chk_acev_type
                CHECK (event_type IN (
                    'STUDENT_CLASSES_START', 'STUDENT_CLASSES_END', 'STUDENT_RECESS',
                    'INSTITUTIONAL_PREPARATION', 'INSTITUTIONAL_DEVELOPMENT',
                    'TEACHER_VACATION', 'EXAM_INTERVAL', 'PEDAGOGICAL_DAY', 'OTHER'
                )),
            ADD CONSTRAINT chk_acev_audience
                CHECK (audience IN ('STUDENTS', 'TEACHERS', 'ADMINISTRATORS', 'ALL')),
            ADD CONSTRAINT chk_acev_exam_period
                CHECK (event_type <> 'EXAM_INTERVAL' OR academic_period_id IS NOT NULL)
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        Schema::dropIfExists('academic_calendar_events');
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
