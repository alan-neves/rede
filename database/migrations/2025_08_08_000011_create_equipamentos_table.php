<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipamentos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('hostname')->unique();
            $table->string('model');
            $table->string('ip');
            $table->string('poe_type');
            $table->string('patrimonio')->nullable();
            $table->integer('qtde_portas');

            $table->foreignId('predio_id')->nullable()->constrained();
            $table->foreignId('rack_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipamentos');
    }
}
