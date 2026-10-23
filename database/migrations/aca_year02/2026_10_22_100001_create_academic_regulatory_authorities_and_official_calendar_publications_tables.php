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

        Schema::create('academic_regulatory_authorities', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('country_code', 2);
            $table->string('authority_code', 80);
            $table->string('official_name', 180);
            $table->timestamps();

            $table->unique(['country_code', 'authority_code'], 'uq_acreg_country_code');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE academic_regulatory_authorities
            ADD CONSTRAINT chk_acreg_country_code
                CHECK (CHAR_LENGTH(country_code) = 2 AND UPPER(country_code) = country_code),
            ADD CONSTRAINT chk_acreg_authority_code
                CHECK (CHAR_LENGTH(TRIM(authority_code)) > 0)
        SQL);

        Schema::create('official_calendar_publications', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('authority_id', 26);
            $table->string('document_type', 40);
            $table->string('document_number', 80)->nullable();
            $table->unsignedSmallInteger('document_year')->nullable();
            $table->string('reference_period', 20);
            $table->string('jurisdiction_code', 40);
            $table->string('regime_code', 40);
            $table->string('region_code', 40);
            $table->char('calendar_code', 1)->nullable();
            $table->string('identity_status', 12)->default('unresolved');
            $table->char('identity_fingerprint', 64)->nullable();
            $table->timestamps();

            $table->foreign('authority_id', 'fk_ocp_authority')
                ->references('id')->on('academic_regulatory_authorities')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->unique('identity_fingerprint', 'uq_ocp_identity_fingerprint');
            $table->index(
                ['authority_id', 'jurisdiction_code', 'regime_code', 'region_code', 'calendar_code', 'reference_period'],
                'ix_ocp_compatibility'
            );
        });

        DB::statement(<<<'SQL'
            ALTER TABLE official_calendar_publications
            ADD CONSTRAINT chk_ocp_calendar_code
                CHECK (calendar_code IS NULL OR calendar_code IN ('A', 'B')),
            ADD CONSTRAINT chk_ocp_identity_status
                CHECK (
                    (identity_status = 'unresolved' AND identity_fingerprint IS NULL)
                    OR
                    (identity_status = 'confirmed' AND identity_fingerprint IS NOT NULL)
                )
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        Schema::dropIfExists('official_calendar_publications');
        Schema::dropIfExists('academic_regulatory_authorities');
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
