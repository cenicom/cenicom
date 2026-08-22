<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table): void {
            $table->char('id', 26)->primary();

            $table->string('code', 20)->unique();

            $table->string('name', 255);

            $table->string(
                'official_registration_country',
                2
            )->nullable();

            $table->string(
                'official_registration_authority',
                150
            )->nullable();

            $table->string(
                'official_registration_value',
                100
            )->nullable();

            $table->string('status', 30)->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
