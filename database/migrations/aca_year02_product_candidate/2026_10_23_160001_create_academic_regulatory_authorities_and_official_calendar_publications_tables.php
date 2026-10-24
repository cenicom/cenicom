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
        Schema::dropIfExists('official_calendar_publications');
        Schema::dropIfExists('academic_regulatory_authorities');
    }


};
