<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table): void {
            $table->char('id', 26)->primary();

            $table->char('institution_id', 26);

            $table->string('code', 40)->unique();

            $table->string('short_code', 20);

            $table->string('name', 255);

            $table->string('ministry_code', 50)->nullable();

            $table->char('address_id', 26);

            $table->string('status', 30)->default('draft');

            $table->timestamps();

            $table->unique([
                'institution_id',
                'short_code',
            ]);

            $table->index('address_id');

            $table->foreign('institution_id')
                ->references('id')
                ->on('institutions');

            $table->foreign('address_id')
                ->references('id')
                ->on('addresses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
