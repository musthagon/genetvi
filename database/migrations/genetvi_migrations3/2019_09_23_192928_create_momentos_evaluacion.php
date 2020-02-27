<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMomentosEvaluacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('momentos_evaluacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('nombre_corto');
            $table->string('descripcion')->nullable();
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
        Schema::dropIfExists('momentos_evaluacion');
    }
}
