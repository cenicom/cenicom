<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega los campos de dominio de countries.
     */
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name');
            $table->string('iso2', 2);
            $table->string('iso3', 3);
        });
    }

    /**
     * Revierte los campos agregados.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'iso2',
                'iso3',
            ]);
        });
    }
};
