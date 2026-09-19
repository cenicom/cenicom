<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega los campos de dominio de currencies.
     */
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('name');
            $table->string('code', 3);
            $table->unsignedTinyInteger('precision')->default(2);
            $table->string('symbol', 10);
            $table->string('decimal_mark', 1)->default('.');
            $table->string('thousands_separator', 1)->default(',');
        });
    }

    /**
     * Revierte los campos agregados.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'code',
                'precision',
                'symbol',
                'decimal_mark',
                'thousands_separator',
            ]);
        });
    }
};
