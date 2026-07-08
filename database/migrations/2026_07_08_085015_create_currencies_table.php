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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->char('code', 3)->unique();
            $table->string('symbol', 10);
            $table->string('name', 100)->index();
            $table->unsignedTinyInteger('decimal_places')
                ->default(2);
            $table->char('decimal_separator', 1)
                ->default('.');
            $table->char('thousands_separator', 1)
                ->default(',');
            $table->string('symbol_position', 10)
                ->default('before');
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('status')->default(true)->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
