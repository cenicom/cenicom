<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->char('code', 2)->unique()->comment('ISO 3166-1 Alpha-2');
            $table->char('iso3', 3)->unique()->comment('ISO 3166-1 Alpha-3');

            $table->string('name', 100);
            $table->string('nationality', 100);

            $table->string('phone_code', 10)->nullable();
            $table->string('currency_code', 5)->nullable();
            $table->string('language', 5)->default('es');

            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
