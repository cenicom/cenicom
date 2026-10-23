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

        Schema::create('official_calendar_publication_versions', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('publication_id', 26);
            $table->unsignedSmallInteger('version_number');
            $table->char('supersedes_version_id', 26)->nullable();
            $table->string('official_version_label', 80)->nullable();
            $table->string('authority_name_snapshot', 180);
            $table->string('title', 500);
            $table->date('promulgated_on')->nullable();
            $table->date('calendar_start_date')->nullable();
            $table->date('calendar_end_date')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('source_reference', 200)->nullable();
            $table->char('document_sha256', 64)->nullable();
            $table->string('verification_status', 12)->default('draft');
            $table->dateTime('validated_at')->nullable();
            $table->dateTime('withdrawn_at')->nullable();
            $table->timestamps();

            // MariaDB 10.4 requires the referenced composite UNIQUE index to exist
            // before the self-referencing foreign key is added by Laravel's schema grammar.
            $table->unique(['publication_id', 'id'], 'uq_ocpv_pub_id');
            $table->unique(['publication_id', 'version_number'], 'uq_ocpv_pub_version');
            $table->index(['verification_status', 'calendar_start_date', 'calendar_end_date'], 'ix_ocpv_status_calendar');
            $table->index(['publication_id', 'supersedes_version_id'], 'ix_ocpv_supersedes');

            $table->foreign('publication_id', 'fk_ocpv_publication')
                ->references('id')->on('official_calendar_publications')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign(['publication_id', 'supersedes_version_id'], 'fk_ocpv_supersedes')
                ->references(['publication_id', 'id'])->on('official_calendar_publication_versions')
                ->onDelete('restrict')->onUpdate('restrict');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE official_calendar_publication_versions
            ADD CONSTRAINT chk_ocpv_version_number
                CHECK (version_number > 0),
            ADD CONSTRAINT chk_ocpv_version_status
                CHECK (verification_status IN ('draft', 'validated', 'available', 'withdrawn')),
            ADD CONSTRAINT chk_ocpv_version_dates
                CHECK (
                    (calendar_start_date IS NULL AND calendar_end_date IS NULL)
                    OR
                    (calendar_start_date IS NOT NULL AND calendar_end_date IS NOT NULL AND calendar_start_date <= calendar_end_date)
                ),
            ADD CONSTRAINT chk_ocpv_state_stamps
                CHECK (
                    (verification_status NOT IN ('validated', 'available')
                        OR (validated_at IS NOT NULL AND calendar_start_date IS NOT NULL AND calendar_end_date IS NOT NULL))
                    AND (verification_status <> 'withdrawn' OR withdrawn_at IS NOT NULL)
                ),
            ADD CONSTRAINT chk_ocpv_available_source
                CHECK (
                    verification_status <> 'available'
                    OR (
                        validated_at IS NOT NULL
                        AND calendar_start_date IS NOT NULL
                        AND calendar_end_date IS NOT NULL
                        AND source_reference IS NOT NULL
                        AND CHAR_LENGTH(TRIM(source_reference)) > 0
                        AND (source_url IS NOT NULL OR document_sha256 IS NOT NULL)
                    )
                ),
            ADD CONSTRAINT chk_ocpv_document_sha
                CHECK (document_sha256 IS NULL OR CHAR_LENGTH(document_sha256) = 64),
            ADD CONSTRAINT chk_ocpv_not_self_supersede
                CHECK (supersedes_version_id IS NULL OR supersedes_version_id <> id)
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        Schema::dropIfExists('official_calendar_publication_versions');
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
