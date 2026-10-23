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
            // Supports a composite FK that guarantees event completion belongs to this year's publication version.
            $table->unique(['publication_version_id', 'id'], 'uq_ay_version_id');
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
        Schema::dropIfExists('academic_years');
    }


};
