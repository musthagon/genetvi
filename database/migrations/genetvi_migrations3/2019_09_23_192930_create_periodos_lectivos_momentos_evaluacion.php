<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodosLectivosMomentosEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodos_lectivos_momentos_evaluacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('periodo_lectivo_id')->unsigned();
            $table->bigInteger('momento_evaluacion_id')->unsigned();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->longText('opciones')->nullable();
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
        Schema::dropIfExists('periodos_lectivos_momentos_evaluacion');
    }
}
