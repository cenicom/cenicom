<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table): void {
            $table->char('id', 26)->primary();

            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');

            $table->string('address', 255)->nullable();
            $table->string('neighborhood', 255)->nullable();

            $table->timestamps();

            $table->foreign('country_id')
                ->references('id')
                ->on('countries');

            $table->foreign('state_id')
                ->references('id')
                ->on('states');

            $table->foreign('city_id')
                ->references('id')
                ->on('cities');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};