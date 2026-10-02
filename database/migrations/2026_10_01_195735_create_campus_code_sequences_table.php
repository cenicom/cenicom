<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_code_sequences', function (Blueprint $table): void {
            $table->char('institution_id', 26);
            $table->string('campus_short_code', 20);
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();

            $table->primary([
                'institution_id',
                'campus_short_code',
            ]);

            $table->foreign('institution_id')
                ->references('id')
                ->on('institutions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_code_sequences');
    }
};
