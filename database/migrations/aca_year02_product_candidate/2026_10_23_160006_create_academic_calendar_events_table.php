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
            // Supports a composite FK from completion records, preventing cross-version completion.
            $table->unique(['publication_version_id', 'id'], 'uq_acev_version_id');
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
        Schema::dropIfExists('academic_calendar_events');
    }


};
