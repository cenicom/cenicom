<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el nombre de los estados.
     */
    public function up(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->string('name');
        });
    }

    /**
     * Revierte el campo agregado.
     */
    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
