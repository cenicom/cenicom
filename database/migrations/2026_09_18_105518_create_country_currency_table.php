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
        Schema::create('country_currency', function (Blueprint $table) {
            $table->foreignUuid('country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignUuid('currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            $table->boolean('is_primary')->default(false);

            $table->primary(['country_id', 'currency_id']);
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_currency');
    }
};
