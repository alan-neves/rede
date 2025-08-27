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
        Schema::table('patch_panel_sala', function (Blueprint $table) {
            $table->foreignId('tipo_porta_id')
              ->nullable()
              ->constrained('tipo_portas')
              ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patch_panel_sala', function (Blueprint $table) {
            //
        });
    }
};
