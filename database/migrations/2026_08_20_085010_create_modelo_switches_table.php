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
        Schema::create('modelo_switches', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('fabricante');
            $table->integer('qtde_portas');
            $table->integer('qtde_portas_poe')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelo_switches');
    }
};
