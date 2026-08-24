<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salas', function (Blueprint $table) {
            $table->decimal('x', 8, 2)->nullable();
            $table->decimal('y', 8, 2)->nullable();
            $table->unsignedBigInteger('planta_id')->nullable();
            $table->foreign('planta_id')->references('id')->on('plantas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salas', function (Blueprint $table) {
            //
        });
    }
};
