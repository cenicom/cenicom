<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {

            $table->uuid('id')->primary();
$table->string('name');

$table->foreignUuid('country_id')->constrained('countries');

            $table->timestamps();

            
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
