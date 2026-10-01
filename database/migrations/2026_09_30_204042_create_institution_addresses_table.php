<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_addresses', function (Blueprint $table): void {
            $table->char('id', 26)->primary();

            $table->char('institution_id', 26);
            $table->char('address_id', 26);

            $table->date('valid_from');
            $table->date('valid_until')->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('institution_addresses');
    }
};
