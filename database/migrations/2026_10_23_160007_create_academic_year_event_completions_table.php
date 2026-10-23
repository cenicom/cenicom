<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * INT-01 product migration candidate: records per-year completion of official calendar events.
 */
return new class extends Migration
{

    public function up(): void
    {
        if (Schema::hasTable('academic_year_event_completions')) {
            throw new RuntimeException(
                'ACA-F02 migration refused: academic_year_event_completions already exists; inspect its provenance.',
            );
        }

        Schema::create('academic_year_event_completions', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->char('id', 26)->primary();
            $table->char('publication_version_id', 26);
            $table->char('academic_year_id', 26);
            $table->char('event_id', 26);
            $table->string('completed_by', 100);
            $table->dateTime('completed_at');
            $table->timestamps();

            $table->foreign(['publication_version_id', 'academic_year_id'], 'fk_ayec_year_version')
                ->references(['publication_version_id', 'id'])->on('academic_years')
                ->restrictOnDelete()->restrictOnUpdate();
            $table->foreign(['publication_version_id', 'event_id'], 'fk_ayec_event_version')
                ->references(['publication_version_id', 'id'])->on('academic_calendar_events')
                ->restrictOnDelete()->restrictOnUpdate();

            $table->unique(
                ['academic_year_id', 'event_id'],
                'uq_ayec_year_event',
            );
            $table->index(['academic_year_id', 'completed_at'], 'ix_ayec_year_completed_at');
            $table->comment('Completion of an official calendar event for an academic year; composite keys enforce publication-version consistency.');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('academic_year_event_completions')) {
            return;
        }

        if (DB::table('academic_year_event_completions')->exists()) {
            throw new RuntimeException(
                'INT-01 rollback refused: completion history exists; preserve records and inspect explicitly.',
            );
        }

        Schema::drop('academic_year_event_completions');
    }


};
