<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_entries', function (Blueprint $table) {
            $table->id();
            $table->string('actor_id')->nullable();
            $table->string('actor_name');
            $table->boolean('actor_authenticated');

            $table->string('action');

            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();

            $table->json('metadata');

            $table->string('result');

            $table->dateTime('occurred_at');

            $table->timestamps();

            $table->index([
                'actor_id',
                'occurred_at',
            ]);

            $table->index([
                'action',
                'occurred_at',
            ]);

            $table->index([
                'subject_type',
                'subject_id',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_entries');
    }
};
