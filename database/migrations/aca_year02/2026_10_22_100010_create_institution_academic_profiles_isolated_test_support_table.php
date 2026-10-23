<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ACA-F05 fixture/schema proposal ONLY for cenicom_aca_year02_isolated_test.
 * This is not yet a production migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->assertIsolatedDatabase();

        if (Schema::connection('mysql')->hasTable('institution_academic_profiles')) {
            throw new RuntimeException(
                'ACA-F05 rechazado: institution_academic_profiles ya existe; inspeccione su origen antes de continuar.',
            );
        }

        foreach (['institutions', 'academic_regulatory_authorities'] as $table) {
            if (! Schema::connection('mysql')->hasTable($table)) {
                throw new RuntimeException(sprintf('ACA-F05 rechazado: falta la tabla %s.', $table));
            }
        }

        Schema::create('institution_academic_profiles', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->char('institution_id', 26)->primary();
            $table->char('country_code', 2);
            $table->string('authority_code', 80);
            $table->string('jurisdiction_code', 40);
            $table->string('regime_code', 40);
            $table->string('region_code', 40);
            $table->char('calendar_code', 1);
            $table->timestamps();

            $table->foreign('institution_id', 'fk_iap_institution')
                ->references('id')->on('institutions')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign(['country_code', 'authority_code'], 'fk_iap_authority')
                ->references(['country_code', 'authority_code'])
                ->on('academic_regulatory_authorities')
                ->onDelete('restrict')->onUpdate('restrict');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE institution_academic_profiles
            ADD CONSTRAINT chk_iap_country_code
                CHECK (CHAR_LENGTH(country_code) = 2 AND UPPER(country_code) = country_code),
            ADD CONSTRAINT chk_iap_authority_code
                CHECK (CHAR_LENGTH(TRIM(authority_code)) > 0),
            ADD CONSTRAINT chk_iap_jurisdiction
                CHECK (CHAR_LENGTH(TRIM(jurisdiction_code)) > 0),
            ADD CONSTRAINT chk_iap_regime
                CHECK (CHAR_LENGTH(TRIM(regime_code)) > 0),
            ADD CONSTRAINT chk_iap_region
                CHECK (CHAR_LENGTH(TRIM(region_code)) > 0),
            ADD CONSTRAINT chk_iap_calendar
                CHECK (calendar_code IN ('A', 'B'))
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();
        Schema::connection('mysql')->dropIfExists('institution_academic_profiles');
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-F05 rechazado: APP_ENV debe ser testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-F05 rechazado: la conexión por defecto debe ser mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-F05 rechazado: existe configuración cacheada.');
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
                    'ACA-F05 rechazado: database.connections.mysql.%s debe ser %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-F05 rechazado: DB_URL debe estar vacío.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-F05 rechazado: DB_PASSWORD debe estar configurada y no vacía.');
        }

        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        if (($row->current_database ?? null) !== 'cenicom_aca_year02_isolated_test') {
            throw new RuntimeException('ACA-F05 rechazado: la base efectiva no es la base aislada autorizada.');
        }
    }
};
