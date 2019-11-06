<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRespuestas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('value_string')->nullable();
            $table->float('value_percentil', 8, 2)->nullable();
            $table->longText('indicador_nombre')->nullable();
            $table->bigInteger('indicador_id')->unsigned();
            $table->bigInteger('categoria_id')->unsigned();
            $table->bigInteger('evaluacion_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('respuestas');
    }
}
