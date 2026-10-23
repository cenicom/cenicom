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
        Schema::create('academic_periods', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('publication_version_id', 26);
            $table->string('code', 20);
            $table->string('name', 100);
            $table->unsignedSmallInteger('sequence_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('declared_duration', 6, 2)->nullable();
            $table->string('duration_unit', 8)->nullable();
            $table->timestamps();

            $table->foreign('publication_version_id', 'fk_apv_publication_version')
                ->references('id')->on('official_calendar_publication_versions')
                ->onDelete('restrict')->onUpdate('restrict');

            $table->unique(['publication_version_id', 'code'], 'uq_apv_version_code');
            $table->unique(['publication_version_id', 'sequence_number'], 'uq_apv_version_sequence');
            $table->unique(['publication_version_id', 'id'], 'uq_apv_version_id');
            $table->index(['publication_version_id', 'start_date', 'end_date'], 'ix_apv_period_dates');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE academic_periods
            ADD CONSTRAINT chk_apv_dates
                CHECK (start_date <= end_date),
            ADD CONSTRAINT chk_apv_sequence
                CHECK (sequence_number > 0),
            ADD CONSTRAINT chk_apv_duration
                CHECK (
                    (declared_duration IS NULL AND duration_unit IS NULL)
                    OR
                    (declared_duration IS NOT NULL AND declared_duration > 0 AND duration_unit IN ('DAYS', 'WEEKS'))
                )
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }


};
