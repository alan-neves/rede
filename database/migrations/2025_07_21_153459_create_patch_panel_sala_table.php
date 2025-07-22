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
        Schema::create('patch_panel_sala', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patch_panel_id')->constrained()->onDelete('cascade');
            $table->foreignId('sala_id')->constrained()->onDelete('cascade');
            $table->integer('porta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_panel_sala');
    }
};
