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
                    (verification_status = 'draft'
                        AND validated_at IS NULL AND withdrawn_at IS NULL)
                    OR
                    (verification_status = 'validated'
                        AND validated_at IS NOT NULL AND withdrawn_at IS NULL
                        AND calendar_start_date IS NOT NULL AND calendar_end_date IS NOT NULL)
                    OR
                    (verification_status = 'available'
                        AND validated_at IS NOT NULL AND withdrawn_at IS NULL
                        AND calendar_start_date IS NOT NULL AND calendar_end_date IS NOT NULL)
                    OR
                    (verification_status = 'withdrawn'
                        AND validated_at IS NOT NULL AND withdrawn_at IS NOT NULL
                        AND calendar_start_date IS NOT NULL AND calendar_end_date IS NOT NULL)
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
        Schema::dropIfExists('official_calendar_publication_versions');
    }


};
