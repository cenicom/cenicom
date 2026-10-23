<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * INT-01 product migration candidate. Review and test against an isolated integration database before deployment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('institution_academic_profiles')) {
            throw new RuntimeException(
                'ACA-F05 rechazado: institution_academic_profiles ya existe; inspeccione su origen antes de continuar.',
            );
        }

        foreach (['institutions', 'academic_regulatory_authorities'] as $table) {
            if (! Schema::hasTable($table)) {
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
        Schema::dropIfExists('institution_academic_profiles');
    }


};
